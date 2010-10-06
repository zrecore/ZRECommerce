<?php

require_once 'library/Zre/Calculator.php';

require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Zre_Calclulator test case.
 */
class Zre_CalclulatorTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Zre_Calclulator
	 */
	private $Zre_Calclulator;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated Zre_CalclulatorTest::setUp()
		

		$this->Zre_Calclulator = new Zre_Calclulator(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated Zre_CalclulatorTest::tearDown()
		

		$this->Zre_Calclulator = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Zre_Calclulator->add()
	 */
	public function testAdd() {
		// TODO Auto-generated Zre_CalclulatorTest->testAdd()
//		$this->markTestIncomplete ( "add test not implemented" );
//		
//		$this->Zre_Calclulator->add(/* parameters */);
		$this->assertEquals($this->Zre_Calclulator->add(1,2), 3);
	
	}
	
	/**
	 * Tests Zre_Calclulator->divide()
	 */
	public function testDivide() {
		// TODO Auto-generated Zre_CalclulatorTest->testDivide()
//		$this->markTestIncomplete ( "divide test not implemented" );
//		
//		$this->Zre_Calclulator->divide(/* parameters */);
		$this->assertEquals($this->Zre_Calclulator->divide(6, 2), 3);
	}
	
	/**
	 * Tests Zre_Calclulator->multiply()
	 */
	public function testMultiply() {
		// TODO Auto-generated Zre_CalclulatorTest->testMultiply()
		$this->markTestIncomplete ( "multiply test not implemented" );
		
		$this->Zre_Calclulator->multiply(/* parameters */);
	
	}
	
	/**
	 * Tests Zre_Calclulator->subtract()
	 */
	public function testSubtract() {
		// TODO Auto-generated Zre_CalclulatorTest->testSubtract()
		$this->markTestIncomplete ( "subtract test not implemented" );
		
		$this->Zre_Calclulator->subtract(/* parameters */);
	
	}

}

