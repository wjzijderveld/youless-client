<?php

namespace spec\Wjzijderveld\Youless\Api;

use Buzz\Browser;
use Buzz\Message\Response;
use DateTime;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Wjzijderveld\Youless\Api\Response\Recent;

class ClientSpec extends ObjectBehavior
{
    private $baseUrl         = 'http://localhost';
    private $recentResponse  = '{"cnt":" 22,285","pwr":764,"lvl":0,"dev":"","det":"","con":"OK","sts":"(06)","raw":0}';
    private $monthResponse   = '{"un":"kWh",
 "tm":"2012-01-01T00:00:00",
 "dt":86400,
 "val":["  0,000","  7,200","  8,000","  8,200","  6,600","  7,900","  8,600"," 10,600",
 "  6,200","  7,000","  8,200","  5,100","  8,900","  5,900","  7,300","  7,400",
 "  6,900"," 12,200","  6,700","  8,500","  7,100","  7,700","  1,300","  8,500",
 "  5,500","  7,700","  7,500"," 10,200","  8,100","  6,300","  4,100",null]}';
    private $dayResponse     = '{"un":"Watt","tm":"2014-09-26T00:00:00","dt":3600,"val":["   90","   90","   70","   80","  100","   70","   90","  160","  270","   70","   90","  120","  120","  100","   80","  180","  140","  190","  110","  150","  160","  170","  160","  150",null]}';

    function let(Browser $browser)
    {
        $browser->beADoubleOf('Buzz\\Browser');
        $this->beConstructedWith($browser, $this->baseUrl);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Wjzijderveld\Youless\Api\Client');
    }

    function it_calls_the_currect_url_for_recent_data(Browser $browser)
    {
        $browser->get($this->baseUrl . '/a?f=j')
            ->shouldBeCalled()
            ->willReturn($this->createResponse('{"pwr":0,"cnt":0}'));

        $this->getRecentData();
    }

    function it_formats_the_output_for_recent_data(Browser $browser)
    {
        $browser->get($this->baseUrl . '/a?f=j')
            ->willReturn($this->createResponse($this->recentResponse));

        $recent = $this->getRecentData();

        $recent->shouldHaveType('Wjzijderveld\Youless\Api\Response\Recent');
        $recent->getPower()->shouldEqual(764.0);
        $recent->getCount()->shouldEqual(22285.0);
    }

    function it_calls_the_correct_url_when_requesting_data_for_a_specific_month(Browser $browser)
    {
        $browser->get($this->baseUrl . '/V?m=1&f=j')->shouldBeCalled()->willReturn($this->createResponse($this->monthResponse));
        $this->getDataForMonth(1);

        $browser->get($this->baseUrl . '/V?m=8&f=j')->shouldBeCalled()->willReturn($this->createResponse($this->monthResponse));
        $this->getDataForMonth(8);
    }

    function it_calls_the_correct_url_when_requesting_data_for_a_specific_day(Browser $browser)
    {
        $browser->get($this->baseUrl . '/V?d=1&f=j')
            ->shouldBeCalled()
            ->willReturn($this->createResponse($this->dayResponse));

        $this->getDataForDay(1);
    }

    function it_returns_the_correct_historical_object_for_month_data(Browser $browser)
    {
        $browser->get($this->baseUrl . '/V?m=5&f=j')
            ->willReturn($this->createResponse($this->monthResponse));

        $data = $this->getDataForMonth(5);

        $data->shouldHaveType('Wjzijderveld\Youless\Api\Response\History');
        $data->getValuesInWatt()->shouldBeLike(array(0, 7200, 8000, 8200, 6600, 7900, 8600, 10600, 6200, 7000, 8200, 5100, 8900, 5900, 7300, 7400, 6900, 12200, 6700, 8500, 7100, 7700, 1300, 8500, 5500, 7700, 7500, 10200, 8100, 6300, 4100));
        $data->getMeasuredFrom()->shouldBeLike(new DateTime('2012-01-01T00:00:00'));
        $data->getDeltaInSeconds()->shouldEqual(86400);
    }

    public function it_returns_the_correct_historical_object_for_day_data(Browser $browser)
    {
        $browser->get($this->baseUrl . '/V?d=3&f=j')
            ->willReturn($this->createResponse($this->dayResponse));

        $data = $this->getDataForDay(3);

        $data->shouldHaveType('Wjzijderveld\Youless\Api\Response\History');
        $data->getValuesInWatt()->shouldBeLike(array(90, 90, 70, 80, 100, 70, 90, 160, 270, 70, 90, 120, 120, 100, 80, 180, 140, 190, 110, 150, 160, 170, 160, 150));
        $data->getMeasuredFrom()->shouldBeLike(new DateTime('2014-09-26T00:00:00'));
        $data->getDeltaInSeconds()->shouldEqual(3600);
    }

    private function createResponse($content, $statusCode = 200)
    {
        $response = new Response();
        $response->setContent($content);
        $response->setHeaders(array(
            'HTTP/1.1 ' . $statusCode . ' REASON',
        ));

        return $response;
    }
}
