<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Client;
use Validator;
use Carbon\Carbon;
use App\Currency;
use App\Rate;
use App\Http\Requests\ConvertRequest;


class RateController extends Controller
{
    /**
     * fetch Fetch new  rates for the current date.
     */
    public function fetch()
    {

       try {
            $xml = simplexml_load_file('http://www.floatrates.com/daily/usd.xml');
            $this->storeCurrency($xml);
            $this->store($xml);
            
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json($e, 500);
        } 
    }


    /**
     * storeCurrency Will store new or  update existing currency
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
     * store Will store or update new rates per day
     * @param  object $xml Fetched XML Object from the link
     */
    public function store($xml)
    {
        $currencies = $xml->item;
        
        foreach ($currencies as $key => $cur) {
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
     * @return json currencies
     */
    public function currencies(){
        $currencies = Currency::all('code','name');
        return response()->json($currencies, 200);
    }

    /**
     * convert This will convert rates based on a specified date. 
     * @param  Convert $request POST parameters (amount,currency, published_at)
     * @return json    Converted amount
     */
    public function convert(Request $request){

        return response()->json($request->all(),200);
        // $validator = Validator::make($request->all(), [
        //     '0'       =>  array('required','regex:/^\d*(\.\d{1,2})?$/'),
        //     '1'     => 'required',
        //     '2' => 'required|date|exists:rates,published_at,currency,'.$request->currency,
        // ],[
        //     'regex'  => 'The :attribute should only have a maximum of 2 decimal places',
        //     'date'   => 'The specified date is not in the correct format.',
        //     'exists' => 'No rates found on the requested date.',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 400);
        // }

        // $rate = Rate::where($request->only(['currency','published_at']))->first();

        // $convertedAmount = $request->amount * $rate->rate;

        // return response()->json($convertedAmount, 200);
        // $client = new \GuzzleHttp\Client();
        // $res = $client->request('POST', 'https://www.dev.pclender.com/pclender/demo/post_demo.php',['data'=>$convertedAmount]);
        // echo $res->getStatusCode();
        // // 200
        // echo $res->getHeaderLine('content-type');
        // // 'application/json; charset=utf8'
        // echo $res->getBody();
        // '{"id": 1420053, "name": "guzzle", ...}'
       

    }


}
