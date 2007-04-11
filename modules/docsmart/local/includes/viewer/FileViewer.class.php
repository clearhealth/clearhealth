<?

$loader->requireOnce('/includes/viewer/Viewer.class.php');
$loader->requireOnce('/includes/viewer/FileDownloader.class.php');

/**
 * This class is a wrapper for viewers and downloader
 * 
 * @see Viewer, PDFViewer, JPEGViewer, FileDownLoader
 *
 */
class FileViewer extends Viewer {
	
	/**
	 * Initializes and runs Viewer
	 *
	 */
	function preview() {
		$this->run();
	}
	
	/**
	 * Initializes and runs FileDownloader
	 *
	 */
	function download() {
		$viewer = new FileDownLoader($this->content, $this->filename);
		$viewer->run();
	}	
	
}





?>