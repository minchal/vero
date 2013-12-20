<?php

use Vero\Cache\Cache;
use Vero\Cache\Backend\Void;
use Vero\I18n\Translator;
use Vero\I18n\Backend\Xliff;
use Vero\I18n\TextDateHelper;

class TextDateHelperTest extends PHPUnit_Framework_TestCase {
	
    private $helper;
    
    public function setUp()
    {
        $i18n = new Translator(new Xliff(new Cache(new Void()), __DIR__.'/../../data/i18n/'), 'pl');
        $this -> helper = new TextDateHelper($i18n);
    }
    
    public function testTextParamsType()
    {
        $fromDateTme = new DateTime('2012-11-16 22:52:10');
        $fromTimestamp = strtotime('2012-11-16 22:52:10');
        $nowDateTime = new DateTime('2013-11-16 22:50:00');
        $nowTimestamp = strtotime('2013-11-16 22:50:00');
        $str = '16 listopada 2012 o 22:52';
        
        $this -> testText($fromDateTme, $str, $nowDateTime);
        $this -> testText($fromDateTme, $str, $nowTimestamp);
        $this -> testText($fromTimestamp, $str, $nowDateTime);
        $this -> testText($fromTimestamp, $str, $nowTimestamp);
    }
    
    /**
     * @dataProvider provider
     */
    public function testText($time, $string, $now = null)
    {
        $this->assertEquals($string, $this -> helper -> format($time, $now));
    }

    public function provider()
    {
        $now = new DateTime();
        $now2 = new DateTime('2013-11-16 22:50:00');
        
        return [
            [new DateTime(), 'teraz', $now],
            [new DateTime('-1 second'), 'sekundę temu', $now],
            [new DateTime('-2 seconds'), '2 sekundy temu', $now],
            [new DateTime('-59 seconds'), '59 sekund temu', $now],
            [new DateTime('-60 seconds'), 'minutę temu', $now],
            [new DateTime('-40 minutes -40 seconds'), '41 minut temu', $now],
            [new DateTime('-59 minutes -29 seconds'), '59 minut temu', $now],
            [new DateTime('-59 minutes -30 seconds'), 'godzinę temu', $now],
            [new DateTime('-59 minutes -31 seconds'), 'godzinę temu', $now],
            [new DateTime('-1 hour'), 'godzinę temu', $now],
            [new DateTime('-1 hour -29 minutes'), 'godzinę temu', $now],
            [new DateTime('-1 hour -30 minutes'), '2 godziny temu', $now],
            [new DateTime('-2 hours'), '2 godziny temu', $now],
            [new DateTime('2013-11-16 10:52:10'), '12 godzin temu', $now2],
            [new DateTime('2013-11-16 07:30:10'), 'dzisiaj o 07:30', $now2],
            [new DateTime('2013-11-15 22:52:10'), 'wczoraj o 22:52', $now2],
            [new DateTime('2013-11-15 00:00:00'), 'wczoraj o 00:00', $now2],
            [new DateTime('2013-11-14 22:52:10'), 'przedwczoraj o 22:52', $now2],
            [new DateTime('2013-11-14 00:00:00'), 'przedwczoraj o 00:00', $now2],
            [new DateTime('2013-11-11 07:52:10'), 'w poniedziałek o 07:52', $now2],
            [new DateTime('2013-11-10 22:52:10'), 'w niedzielę o 22:52', $now2],
            [new DateTime('2013-11-09 22:52:10'), '9 listopada o 22:52', $now2],
            [new DateTime('2013-05-16 22:52:10'), '16 maja o 22:52', $now2],
            [new DateTime('2012-11-16 22:52:10'), '16 listopada 2012 o 22:52', $now2],
        ];
    }
}
