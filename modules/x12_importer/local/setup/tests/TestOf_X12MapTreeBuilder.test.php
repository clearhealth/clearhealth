<?php
require_once dirname(__FILE__) . '/config.php';
$GLOBALS['loader']->requireOnce('includes/X12MapTreeBuilder.class.php');
$GLOBALS['loader']->requireOnce('includes/X12Objects.class.php');

class TestOf_X12MapTreeBuilder extends UnitTestCase
{
	function setUp() {
		$isa = new X12Block();
		$isa->code = 'ISA';
		$isa->id = 'InterchangeHeader';
		
		$gs = new X12Block();
		$gs->code = 'GS';
		$gs->id = 'GroupStart';
		
		$nm1 = new X12Block();
		$nm1->code = 'NM1';
		$nm1->id = 'PatientName';
		
		$n3 = new X12Block();
		$n3->code = 'N3';
		$n3->id = 'AddressOne';
		
		$n4 = new X12Block();
		$n4->code = 'N4';
		$n4->id = 'AddressTwo';
		
		$ref = new X12Block();
		$ref->code = 'REF';
		$ref->id = 'SecondaryIdentifier';
		
		$ge = new X12Block();
		$ge->code = 'GE';
		$ge->id = 'GroupEnd';
		
		$iea = new X12Block();
		$iea->code = 'IEA';
		$iea->id = 'InterchangeEnd';
		
		$this->testRawTree = array(
			$isa, $gs,
			$nm1, $n3, $n4, $ref,
			$ge, $iea
		);

	}
	
	function testBasicAPI() {
		$testTree = array(
			'header' => array('ISA', 'GS'),
			'patient' => array('NM1', 'N3', 'N4', 'REF'),
			'footer' => array('GE', 'IEA')
		);
				
		$builder = new X12MapTreeBuilder();
		$builder->setMapTree($testTree);
		$builder->setRawObjectTree($this->testRawTree);
		
		$builtTree = $builder->getBuiltTree();
		$this->assertEqual($builtTree,
			array(
				'header' => array(	
					'InterchangeHeader' => $this->testRawTree[0],
					'GroupStart' => $this->testRawTree[1]
				),
				'patient' => array(
					'PatientName' => $this->testRawTree[2],
					'AddressOne' => $this->testRawTree[3],
					'AddressTwo' => $this->testRawTree[4],
					'SecondaryIdentifier' =>  $this->testRawTree[5]
					
				),
				'footer' => array(
					'GroupEnd' => $this->testRawTree[6],
					'InterchangeEnd' => $this->testRawTree[7]
				)
			)
		);
	}
	
	function testNestedBlocks() {
		$testTree = array(
			'header' => array('ISA', 'GS'),
			'patient' => array('NM1', 'address' => array('N3', 'N4'), 'REF'),
			'footer' => array('GE', 'IEA')
		);
				
		$builder = new X12MapTreeBuilder();
		$builder->setMapTree($testTree);
		$builder->setRawObjectTree($this->testRawTree);
		$builtTree = $builder->getBuiltTree();

		$this->assertEqual($builtTree,
			array(
				'header' => array(	
					'InterchangeHeader' => $this->testRawTree[0],
					'GroupStart' => $this->testRawTree[1]
				),
				'patient' => array(
					'PatientName' => $this->testRawTree[2],
					'address' => array(
						'AddressOne' => $this->testRawTree[3],
						'AddressTwo' => $this->testRawTree[4],
					),
					'SecondaryIdentifier' =>  $this->testRawTree[5]
					
				),
				'footer' => array(
					'GroupEnd' => $this->testRawTree[6],
					'InterchangeEnd' => $this->testRawTree[7]
				)
			)
		);
	}
	
	function testBasicLoopingData() {
		$testTree = array(
			'header' => array('ISA', 'GS'),
			'patient+' => array('NM1', 'N3', 'N4', 'REF'),
			'footer' => array('GE', 'IEA')
		);
		
		$isa = new X12Block();
		$isa->code = 'ISA';
		$isa->id = 'InterchangeHeader';
		
		$gs = new X12Block();
		$gs->code = 'GS';
		$gs->id = 'GroupStart';
		
		$nm1 = new X12Block();
		$nm1->code = 'NM1';
		$nm1->id = 'PatientName';
		
		$n3 = new X12Block();
		$n3->code = 'N3';
		$n3->id = 'AddressOne';
		
		$n4 = new X12Block();
		$n4->code = 'N4';
		$n4->id = 'AddressTwo';
		
		$ref = new X12Block();
		$ref->code = 'REF';
		$ref->id = 'SecondaryIdentifier';
		
		$ge = new X12Block();
		$ge->code = 'GE';
		$ge->id = 'GroupEnd';
		
		$iea = new X12Block();
		$iea->code = 'IEA';
		$iea->id = 'InterchangeEnd';
		
		$this->testRawTree = array(
			$isa, $gs,
			$nm1, $n3, $n4, $ref,
			$nm1, $n3, $n4, $ref,
			$ge, $iea
		);
		
				
		$builder = new X12MapTreeBuilder();
		$builder->setMapTree($testTree);
		$builder->setRawObjectTree($this->testRawTree);
		
		$builtTree = $builder->getBuiltTree();
		$this->assertEqual($builtTree,
			array(
				'header' => array(	
					'InterchangeHeader' => $this->testRawTree[0],
					'GroupStart' => $this->testRawTree[1]
				),
				'patient' => array(
					array(
						'PatientName' => $this->testRawTree[2],
						'AddressOne' => $this->testRawTree[3],
						'AddressTwo' => $this->testRawTree[4],
						'SecondaryIdentifier' =>  $this->testRawTree[5]
					),
					array(
						'PatientName' => $this->testRawTree[6],
						'AddressOne' => $this->testRawTree[7],
						'AddressTwo' => $this->testRawTree[8],
						'SecondaryIdentifier' =>  $this->testRawTree[9]
					),
					
				),
				'footer' => array(
					'GroupEnd' => $this->testRawTree[10],
					'InterchangeEnd' => $this->testRawTree[11]
				)
			)
		);
 
	}
	
	
	function testNestedLooping() {
		$testTree = array(
			'header' => array('ISA', 'GS'),
			'patient+' => array('NM1', 'address+' => array('N3', 'N4'), 'REF'),
			'footer' => array('GE', 'IEA')
		);
		
		$isa = new X12Block();
		$isa->code = 'ISA';
		$isa->id = 'InterchangeHeader';
		
		$gs = new X12Block();
		$gs->code = 'GS';
		$gs->id = 'GroupStart';
		
		$nm1 = new X12Block();
		$nm1->code = 'NM1';
		$nm1->id = 'PatientName';
		
		$n3 = new X12Block();
		$n3->code = 'N3';
		$n3->id = 'AddressOne';
		
		$n4 = new X12Block();
		$n4->code = 'N4';
		$n4->id = 'AddressTwo';
		
		$ref = new X12Block();
		$ref->code = 'REF';
		$ref->id = 'SecondaryIdentifier';
		
		$ge = new X12Block();
		$ge->code = 'GE';
		$ge->id = 'GroupEnd';
		
		$iea = new X12Block();
		$iea->code = 'IEA';
		$iea->id = 'InterchangeEnd';
		
		$this->testRawTree = array(
			$isa, $gs,
			$nm1, $n3, $n4, $n3, $n4, $ref,
			$nm1, $n3, $n4, $ref,
			$ge, $iea
		);
		
				
		$builder = new X12MapTreeBuilder();
		$builder->setMapTree($testTree);
		$builder->setRawObjectTree($this->testRawTree);
		
		$builtTree = $builder->getBuiltTree();
		$expectedTree = array(
			'header' => array(	
				'InterchangeHeader' => $this->testRawTree[0],
				'GroupStart' => $this->testRawTree[1]
			),
			'patient' => array(
				array(
					'PatientName' => $this->testRawTree[2],
					'address' => array(
						array(
							'AddressOne' => $this->testRawTree[3],
							'AddressTwo' => $this->testRawTree[4]
						),
						array(
							'AddressOne' => $this->testRawTree[5],
							'AddressTwo' => $this->testRawTree[6]
						),
					),
					'SecondaryIdentifier' =>  $this->testRawTree[7]
				),
				array(
					'PatientName' => $this->testRawTree[8],
					'address' => array(
						array(
							'AddressOne' => $this->testRawTree[9],
							'AddressTwo' => $this->testRawTree[10],
						)
					),
					'SecondaryIdentifier' =>  $this->testRawTree[11]
				),
				
			),
			'footer' => array(
				'GroupEnd' => $this->testRawTree[12],
				'InterchangeEnd' => $this->testRawTree[13]
			)
		);
		$this->assertEqual($builtTree, $expectedTree);
 
	}
}

if (!defined('X12_IMPORTER_GROUP_TEST')) {
	$test = new TestOf_X12MapTreeBuilder();
	require X12_PARSER_TEST_PATH . '/testRunner.php';
}
?>
