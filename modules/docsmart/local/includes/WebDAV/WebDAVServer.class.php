<?php

include('HTTP/WebDAV/Server.php');
$loader->requireOnce('/datasources/TreeFSView_DS.class.php');
$loader->requireOnce('/includes/storage/RevisionStorage.class.php');
$loader->requireOnce('/includes/WebDAV/WebDAVUtils.class.php');
$loader->requireOnce('/includes/WebDAV/WebDAVNode.class.php');
$loader->requireOnce('/includes/storage/FileStorageFS.class.php');
$loader->requireOnce('/includes/viewer/Viewer.class.php');

/**
 * DocSmart WebDAVServer
 *
 */
class WebDAVServer extends HTTP_WebDAV_Server {

        /**
         * GET method handler
         * 
         * @param  array  parameter passing array
         * @return bool   true on success
         */
         function GET(&$options) {
			$node = WebDAVUtils::getNode($options['path']);

			if(!$node->node_type) {
				return false;	
			}
			
        	if($node->isDir()) {
        		$tree = new TreeFSView_DS($node->treeNode->tree_id, $node->treeNode->level);
        		return $this->showFiles($tree, $options);	
        	}

            // detect resource type
            $options['mimetype'] = $node->mimetype; 
            $options['mtime'] = $node->mtime;

            // detect resource size
            $options['size'] = $node->filesize;
            
            // no need to check result here, it is handled by the base class
            $storage = new RevisionStorage($node->revision_id, $node->storage_type);
            $options['data'] = $storage->getFile();
            
            return true;

         }
         
        /**
         * PROPFIND method handler
         *
         * @param  array  general parameter passing array
         * @param  array  return array for file properties
         * @return bool   true on success
         */
        function PROPFIND(&$options, &$files) {
            // get absolute fs path to requested resource
            $node = WebDAVUtils::getNode($options['path']);
            
			if(!$node) {
				return false;	
			}            

            // prepare property array
            $files["files"] = array();

            // store information for the requested path itself
            $files["files"][] = $this->fileinfo($node->toArray());
            
            // information for contained resources requested?
            if (!empty($options["depth"]))  { // TODO check for is_dir() first?
                
                // make sure path ends with '/'
                $options["path"] = HTTP_WebDAV_Server::_slashify($options["path"]);

                // try to open directory
                if($node->isDir()) {
                	$tree = new TreeFSView_DS($node->treeNode->tree_id, $node->treeNode->level);
	                foreach($tree->toArray() as $n) {
	                	  $n['path'] = $options["path"].$n['filename'];
	                      $files["files"][] = $this->fileinfo($n);
	                }	
                }
            }
            return true;
        } 

        /**
         * PUT method handler
         * 
         * @param  array  parameter passing array
         * @return bool   true on success
	     */
        function PUT(&$options) {
        	$parent = WebDAVUtils::getParentNode($options['path']);

            if (!$parent->isDir() || $parent->treeNode->level <= 0) {
                $this->http_status("409 Conflict");
                exit;
            }

            $node = WebDAVUtils::getNode($options['path']);
            $options["new"] = !$node;
            
            if($options["new"]) {
	            // save storable
	            $storable=& Celini::newOrdo('Storable');
				$storable->populate_array( array(
					'filename' => basename($options['path']),
					'storage_type' => 'FS',
					'mimetype' => Viewer::mimeContentType(basename($options['path'])) ));	
				$storable->persist();
            }else{
            	$storable=& Celini::newOrdo('Storable',$node->node_id);
            }
			
			// save revision
			$revision =& Celini::newOrdo('Revision');
			$revision->populate_array( array(
				'storable_id' => $storable->storable_id,
				'create_date' => date('Y-m-d H:i:s') ));
			$revision->persist();
			
			// update last_revision_id for the storable
			$storable->set('last_revision_id', $revision->revision_id);
			$storable->persist();			

			if($options["new"]) {
				// add storable node to the tree
				$treeNode =& Celini::newOrdo('TreeNode');
				$treeNode->populate_array( array(
						'node_type' => 'storable',
						'node_id' => $storable->storable_id ));
				$treeNode->insert($parent->treeNode->tree_id);
			}	

			$tempnam = WebDAVUtils::getTmpFileName();
			$stat = fopen($tempnam, "w"); 
			
           if ($stat === false) {
                $stat = "403 Forbidden";
            } else if (is_resource($stat) && get_resource_type($stat) == "stream") {
                $stream = $stat;

                $stat = $options["new"] ? "201 Created" : "204 No Content";

                if (!empty($options["ranges"])) {
                	$range = $options["ranges"][0];
                    // TODO multipart support is missing (see also above)
                    if (0 == fseek($stream, $range[0]["start"], SEEK_SET)) {
                        $length = $range[0]["end"]-$range[0]["start"]+1;
                        if (!fwrite($stream, fread($options["stream"], $length))) {
                            $stat = "403 Forbidden"; 
                        }
                    } else {
                        $stat = "403 Forbidden"; 
                    }
                } else {
                    while (!feof($options["stream"])) {
                        if (false === fwrite($stream, fread($options["stream"], 4096))) {
                            $stat = "403 Forbidden"; 
                            break;
                        }
                    }
                }
                fclose($stream);            
            } 

			$revisionStorage = new RevisionStorage($revision->revision_id, $storable->storage_type);
			$revisionStorage->saveFile($tempnam);

			$revision->set('filesize', filesize($tempnam));
			$revision->persist();
			unlink($tempnam);
            $this->http_status($stat);
			exit;		
        }

        /**
         * MKCOL method handler
         *
         * @param  array  general parameter passing array
         * @return bool   true on success
         */
        function MKCOL($options) {  
            $parent = WebDAVUtils::getParentNode($options['path']);
            
            if (!$parent) {
                return "409 Conflict";
            }

            $path = pathinfo($options['path']);
            $name = $path['basename'];
            
            if (!$parent->isDir()) {
                return "403 Forbidden";
            }

            if (!empty($_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
                return "415 Unsupported media type";
            }
            
            // add folder
            $folder =& Celini::newOrdo('Folder');
            $folder->populate_array(array('label' => $name));
            $folder->persist();

            if (!$folder->folder_id) {
                return "403 Forbidden";                 
            }            
            
            // insert tree node
			$node =& Celini::newOrdo('TreeNode');
			$data = array(
				'node_id' => $folder->folder_id,
				'node_type' => 'folder');
			$node->populate_array($data);
			$node->insert($parent->treeNode->tree_id);

            if (!$node->tree_id) {
                return "403 Forbidden";                 
            }			
			
	        return ("201 Created");
        }


        /**
         * MOVE method handler
         *
         * @param  array  general parameter passing array
         * @return bool   true on success
         */
        function MOVE($options) {
            return $this->COPY($options, true);
        }

        /**
         * COPY method handler
         *
         * @param  array  general parameter passing array
         * @return bool   true on success
         */
        function COPY($options, $del=false) {
            // TODO Property updates still broken (Litmus should detect this?)

            if (!empty($_SERVER["CONTENT_LENGTH"])) { // no body parsing yet
                return "415 Unsupported media type";
            }

            // no copying to different WebDAV Servers yet
            if (isset($options["dest_url"])) {
                return "502 bad gateway";
            }

            $source = WebDAVUtils::getNode($options['path']);
            if (!$source) return "404 Not found";

            $dest = WebDAVUtils::getNode($options["dest"]);

            $new = !$dest;
            $existing_col = false;

            if (!$new) {
                if ($del && WebDAVUtils::isDir($dest)) {
                    if (!$options["overwrite"]) {
                        return "412 precondition failed";
                    }
                    print_r($dest);
                    print_r($source);
                    print_r($options);
                    exit;
                    $dest .= basename($source);
                    if (file_exists($dest)) {
                        $options["dest"] .= basename($source);
                    } else {
                        $new = true;
                        $existing_col = true;
                    }
                }
            }

            if (!$new) {
                if ($options["overwrite"]) {
                    $stat = $this->DELETE(array("path" => $options["dest"]));
                    if (($stat{0} != "2") && (substr($stat, 0, 3) != "404")) {
                        return $stat; 
                    }
                } else {                
                    return "412 precondition failed";
                }
            }

           if ($source->isDir() && ($options["depth"] != "infinity")) {
                // RFC 2518 Section 9.2, last paragraph
                return "400 Bad request";
            }

            if ($del) {
				if(!WebDAVUtils::moveNode($source, $options["dest"])) {				
                    return "500 Internal server error";
                }
             } else {
                   $stat = WebDAVUtils::copyNode($source, $options["dest"]);
                   if($stat) {
                   		return $stat;
                   }
//                foreach ($files as $file) {
//                    if (is_dir($file)) {
//                      $file = $this->_slashify($file);
//                    }
//
//                    $destfile = str_replace($source, $dest, $file);
//                    
//                    if (is_dir($file)) {
//                        if (!is_dir($destfile)) {
//                            // TODO "mkdir -p" here? (only natively supported by PHP 5) 
//                            if (!mkdir($destfile)) {
//                                return "409 Conflict";
//                            }
//                        } else {
//                          error_log("existing dir '$destfile'");
//                        }
//                    } else {
//                        if (!copy($file, $destfile)) {
//                            return "409 Conflict";
//                        }
//                    }
//                }

            }

            return ($new && !$existing_col) ? "201 Created" : "204 No Content";         
        }

        /**
         * DELETE method handler
         *
         * @param  array  general parameter passing array
         * @return bool   true on success
         */
        function DELETE($options) {
            $node = WebDAVUtils::getNode($options["path"]);

            if (!$node->node_type) {
                return "404 Not found";
            }
			$folder =& Celini::newOrdo('TreeNode', $node->treeNode->tree_id);
			$folder->delete(true);

            return "204 No Content";
        }        
        
	/**
	 * Get properties for a single file/resource
	 *
	 * @param  string  resource path
	 * @return array   resource properties
	 */
	function fileinfo(&$node) {

	    // create result array
	    $info = array();
	    // TODO remove slash append code when base clase is able to do it itself

		$info["path"]  = $node['path'] = ($node['node_type'] == 'folder') ? $this->_slashify($node['path']) : $node['path'];

	    //$info["path"]  = $node['node_id'];
	    $info["props"] = array();
	    
	    // no special beautified displayname here ...
	    //$info["props"][] = $this->mkprop("displayname", strtoupper($node['path']));
	    $info["props"][] = $this->mkprop("displayname", "/".strtoupper($node['displayname']));
	    
	    // creation and modification time
	    $info["props"][] = $this->mkprop("creationdate",    $node['ctime']);
	    $info["props"][] = $this->mkprop("getlastmodified", $node['mtime']);
	
	    // type and size (caller already made sure that path exists)
	    if (WebDAVUtils::isDir($node)) {
	        // directory (WebDAV collection)
	        $info["props"][] = $this->mkprop("resourcetype", "collection");
	        $info["props"][] = $this->mkprop("getcontenttype", "httpd/unix-directory");             
	        $info["props"][] = $this->mkprop("getcontentlength", 4096);
	    } else {
	        // plain file (WebDAV resource)
	        $info["props"][] = $this->mkprop("resourcetype", "");
	        //if (is_readable($fspath)) {
	            $info["props"][] = $this->mkprop("getcontenttype", $node['mimetype']);
	        //} else {
	        //    $info["props"][] = $this->mkprop("getcontenttype", "application/x-non-readable");
	        //}               
	        $info["props"][] = $this->mkprop("getcontentlength", $node['filesize']);
	    }
	
	    return $info;
	}	

        /**
         * GET method handler for directories
         *
         * This is a very simple mod_index lookalike.
         * See RFC 2518, Section 8.4 on GET/HEAD for collections
         *
         * @param  string  directory path
         * @return void    function has to handle HTTP response itself
         */
        function showFiles($tree, &$options) {

      	
            // fixed width directory column format
            $format = "%15s  %-19s  %-s\n";

            echo "<html><head><title>Index of ".htmlspecialchars($options['path'])."</title></head>\n";
            
            echo "<h1>Index of ".htmlspecialchars($options['path'])."</h1>\n";
            
            echo "<pre>";
            printf($format, "Size", "Last modified", "Filename");
            echo "<hr>";

			foreach($tree->toArray() as $node) {
                    printf($format, 
                           number_format((WebDAVUtils::isDir($node))?"4096":$node['filesize']),
                           strftime("%Y-%m-%d %H:%M:%S", $node['mtime']), 
                           "<a href='".urlencode($node['filename'])."'>".$node['filename']."</a>");				
			}

			echo "</pre>";

            echo "</html>\n";

            exit;
        }         
		
}


?>
