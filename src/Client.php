<?php

namespace Wjzijderveld\Youless\Api;

use Assert\Assertion as Assert;
use Buzz\Browser;
use Buzz\Message\Response;
use DateTime;
use Wjzijderveld\Youless\Api\Response\History;
use Wjzijderveld\Youless\Api\Response\Recent;

class Client
{
    const TYPE_RECENT     = 'a';
    const TYPE_HISTORICAL = 'V';

    const FORMAT_JSON = 'j';

    private $browser;
    private $baseUrl;
    private $format;

    public function __construct(Browser $browser, $baseUrl, $format = self::FORMAT_JSON)
    {
        $this->browser   = $browser;
        $this->baseUrl   = $baseUrl;
        $this->format    = $format;
    }

    private function createDataUrl($type, array $arguments = array())
    {
        $arguments['f'] = $this->format;

        return $this->baseUrl . '/' . $type . '?' . http_build_query($arguments);
    }

    /**
     * @return Recent
     */
    public function getRecentData()
    {
        $response = $this->browser->get($this->createDataUrl(self::TYPE_RECENT));

        if ($response->isOK()) {
            $raw = json_decode($response->getContent(true), true);
            return new Recent($this->float($raw['pwr']), $this->float($raw['cnt']) * 1000);
        }
    }

    /**
     * @param string $month
     *
     * @return History
     */
    public function getDataForMonth($month)
    {
        Assert::range($month, 1, 12);

        $response = $this->browser->get($this->createDataUrl(self::TYPE_HISTORICAL, array('m' => $month)));

        return $this->processHistoricalResponse($response);
    }

    /**
     * @param string $day
     *
     * @return History
     */
    public function getDataForDay($day)
    {
        Assert::range($day, 0, 6);

        $response = $this->browser->get($this->createDataUrl(self::TYPE_HISTORICAL, array('d' => $day)));

        return $this->processHistoricalResponse($response);
    }

    /**
     * @param integer $interval (1,2 or 3)
     *
     * @return History
     */
    public function getDataFor8Hours($interval = 1)
    {
        Assert::range($interval, 1, 3);

        $response = $this->browser->get($this->createDataUrl(self::TYPE_HISTORICAL, array('w' => $interval)));

        return $this->processHistoricalResponse($response);
    }

    /**
     * @param integer $interval (1 or 2)
     *
     * @return History
     */
    public function getDataPer30Minutes($interval = 1)
    {
        Assert::range($interval, 1, 2);

        $response = $this->browser->get($this->createDataUrl(self::TYPE_HISTORICAL, array('h' => $interval)));

        return $this->processHistoricalResponse($response);
    }

    private function processHistoricalResponse(Response $response)
    {
        if ($response->isOK()) {
            $data = json_decode($response->getContent(true), true);

            $values = array_map(array($this, 'float'), $data['val']);
            array_pop($values); // remove the last null value...

            return new History(new DateTime($data['tm']), $values, $data['un'], (int) $data['dt']);
        }
    }

    private function float($value)
    {
        return (float) str_replace(',', '.', $value);
    }
}
