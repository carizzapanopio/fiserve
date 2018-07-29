<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Client;
use Validator;
use Carbon\Carbon;
use App\Currency;
use App\Rate;


class CurrencyController extends Controller
{
    /*
200: OK. The standard success code and default option.
201: Object created. Useful for the store actions.
204: No content. When an action was executed successfully, but there is no content to return.
     */

    /**
     * [fetch description]
     * @return [type] [description]
     */
    public function fetch()
    {

       try {
            $xml = simplexml_load_file('http://www.floatrates.com/daily/usd.xml');

            $this->storeCurrency($xml);
            $this->storeRates($xml);
            
            return response()->json(null,204);
        } catch (Exception $e) {
            return response()->json($e,400);
        } 
    }
    /**
     * insertOrUpdate Will store new currency
     * @param  object $xml Fetched XML Object from the link
     */
    public function storeCurrency($xml)
    {
        $currencies = $xml->item;
        foreach ($currencies as $key => $cur) {
            Currency::updateOrCreate([
                'code'       => $cur->targetCurrency
            ],[
                'name'       => $cur->targetName,
            ]);
        }
    }

    /**
     * storeRates Will store or update new rates per day
     * @param  object $xml Fetched XML Object from the link
     */
    public function storeRates($xml)
    {
        $currencies = $xml->item;

        
        foreach ($currencies as $key => $cur) {
            /**
             * "targetCurrency"
             * exchangeRate
             * baseCurrency
             * inverseRate
             * pubDate
             */
            Rate::updateOrCreate([
                'currency'      => $cur->targetCurrency,
                'base_currency' => $cur->baseCurrency,
                'published_at'  => Carbon::parse($cur->pubDate)->format('Y-m-d'),
            ],[
                'rate'          => str_replace(',', '', $cur->exchangeRate),
                'inverse_rate'  => str_replace(',', '', $cur->inverseRate),

            ]);

        }
    }

    /**
     * all This will return a list of all currencies.
     * @return array currencies
     */
    public function all(){
        $currencies = Currency::all('code','name');
        return response()->json($currencies);
    }
    /**
     * [convert description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function convert(Request $request){

        /**
         * amount
         * currency code
         * published_at (Y-m-d),
         * regex:/^-?[0-9]+(?:\.[0-9]{1,2})?$/
         */
        
        // $validatedData = $request->validate([
        //     'amount'       => 'required',
        //     'currency'     => 'required',
        //     'published_at' => 'required|date',
        // ]);

        $validator = Validator::make($request->all(), [
            'amount'       =>  array('required','regex:/^\d*(\.\d{1,2})?$/'),
            'currency'     => 'required',
            'published_at' => 'required|date',
        ],[
            'regex' => 'The :attribute should only have a maximum of 2 decimal places',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // var_dump($validatedData->errors());
        //check if a conversion is available for the said currency and date
        // var_dump($validatedData);
        $rate = Rate::where($request->only(['currency','published_at']))->first();
        if($rate){

            $convertedAmount = $request->amount * $rate->rate;

            return response()->json($convertedAmount, 200);
            $client = new \GuzzleHttp\Client();
            $res = $client->request('POST', 'https://www.dev.pclender.com/pclender/demo/post_demo.php',['data'=>$convertedAmount]);
            // echo $res->getStatusCode();
            // // 200
            // echo $res->getHeaderLine('content-type');
            // // 'application/json; charset=utf8'
            // echo $res->getBody();
            // '{"id": 1420053, "name": "guzzle", ...}'
        }else{
            //no rate found on the said date.
            echo "No rate found on the said date";
        }     

    }


}
