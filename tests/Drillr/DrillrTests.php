<?php
/*
 * This file is part of the Drillr package, a project by Igor Soares.
 *
 * (c) 2016 Igor Soares
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require '/../../src/Drillr/Drillr.php';
class DrillrTests extends PHPUnit_Framework_TestCase
{
    public function testSingletonInstance()
    {
        $drillr = Drillr::getInstance();
        $this->assertInstanceOf('Drillr', $drillr);
    }            

    public function testLoadBlock()
    {
        $drillr = Drillr::getInstance();
        $block = $drillr->addToPath(__DIR__.'/public/')->loadBlock('DrillrTest.html');
        $this->assertNotEmpty($block->_expose('block'));
    }            

    public function testChangePath()
    {
        $drillr = Drillr::getInstance();
        //as setted from the last call
        $previous_path = $drillr->_expose('path');
        $drillr->changePath(__DIR__.'/sample/');
        $current_path = $drillr->_expose('path');
        $this->assertNotEquals($previous_path, $current_path);
    }            

    public function testMultipleBlocks()
    {
        $drillr = Drillr::getInstance();
        $previous_block = $drillr->_expose('block');
        $drillr->changePath(__DIR__.'/public/')->loadBlock('DrillrTest2.html');
        $current_block = $drillr->_expose('block');
        $this->assertNotEquals($previous_block, $current_block);
    }      

    public function testCollection()
    {
        $drillr = Drillr::getInstance();
        $collection = array( array('test_data' => 'foo'), array('test_data' => 'bar') );
        $this->assertNotEmpty($drillr->drill($collection));
    }

    public function testCollectionWithFilter()
    {
        function testFilter($param)
        {
            return 'filtered '. $param;
        }
        $drillr = Drillr::getInstance();
        $collection = array( array('test_data' => 'foo'), array('test_data' => 'bar') );
        $this->assertNotEmpty($drillr->addFilter('testFilter',array('test_data'), 'test_data')->drill($collection));
    }

    public function testNoFilterAfterBlockDrill()
    {
        $drillr = Drillr::getInstance();
        $filters = $drillr->_expose('filter');
        $this->assertEmpty($filters);
    }

    //TODO: Finish tests

}