<?php

namespace App\Http\Controllers;

use App\Models\Precinct;
use Illuminate\Http\Request;

class PrecinctController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $precincts=Precinct::orderBy('code','asc')->paginate(20);
        return view('precinct.index', compact('precincts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('precinct.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input=$request->all();
        $precinct=Precinct::create($input);
        return redirect('/precinct/'.$precinct->id);
    }


    public function fetchDataIEBC(Request $request)
    {
        for($i=1;$i<27397;$i++){
        $y=floor($i/1000);
        $url = 'https://forms.iebc.or.ke/assets/data/precincts/'.$y.'/s'.$i.'.json';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "authority: forms.iebc.or.ke",
            "method: GET",
            "path: /assets/data/totalized_results/sites/6/6816_F.json",
            "scheme: https",
            "accept: application/json, text/plain, */*",
            "accept-encoding: gzip, deflate, br",
            "accept-language: en-US,en;q=0.9",
            "referer: https://forms.iebc.or.ke/",
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: 'Windows'",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($curl);
        $centers=json_decode($curl_response);
        if(is_array($centers) && $centers!=null){
        foreach($centers as $center){
            $precincta=Precinct::where('code','=',$center->cc)->first();
            if(!isset($precincta)){
            $precinctD=['code'=>$center->cc,'name'=>$center->n,'34A'=>$i];
            $precinct=Precinct::create($precinctD);
            }
        }
        }
        }

    }

    public function fetchFormsIEBC(Request $request)
    {
        for($i=1;$i<27397;$i++){
        $y=floor($i/1000);
        $url='https://forms.iebc.or.ke/assets/data/totalized_results/sites/'.$y.'/'.$i.'_F.json';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "authority: forms.iebc.or.ke",
            "method: GET",
            "path: /assets/data/totalized_results/sites/6/6816_F.json",
            "scheme: https",
            "accept: application/json, text/plain, */*",
            "accept-encoding: gzip, deflate, br",
            "accept-language: en-US,en;q=0.9",
            "referer: https://forms.iebc.or.ke/",
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: 'Windows'",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36",
        );
        $headersPDF = array(
            "authority: forms.iebc.or.ke",
            "method: GET",
            "path: /assets/data/totalized_results/sites/6/6816_F.json",
            "scheme: https",
            "Content-type: application/pdf",
            "Cache-Control: public",
            "accept-language: en-US,en;q=0.9",
            "referer: https://forms.iebc.or.ke/",
            "sec-ch-ua-mobile: ?0",
            "sec-ch-ua-platform: 'Windows'",
            "sec-fetch-dest: empty",
            "sec-fetch-mode: cors",
            "sec-fetch-site: same-origin",
            "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36",
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($curl);
        $forms=json_decode($curl_response);
        if(is_array($forms) && $forms!=null){
        $items=$forms[0]->forms[0]->nl;
        foreach($items as $item){
            if(isset($item->path)){
            $precinct_code=explode("_",$item->path)[3];
            $precinct=Precinct::where('code','=',$precinct_code)->first();
            if(isset($precinct) && $precinct->link == null){
                $precinct->update(['link'=>$item->path]);
                $output_filename = $precinct_code.".pdf";
                $host = "https://forms.iebc.or.ke/".$item->path;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $host);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headersPDF);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_AUTOREFERER, false);
                curl_setopt($ch, CURLOPT_REFERER, "https://forms.iebc.or.ke/");
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $result = curl_exec($ch);
                curl_close($ch);
                /*print_r($result); */
                $fp = fopen($output_filename, 'w+');
                fwrite($fp, $result);
                fclose($fp);
            }
            }
        }
        }
        }

    }


    

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Precinct  $precinct
     * @return \Illuminate\Http\Response
     */
    public function show(Precinct $precinct)
    {
        //
        return view('precinct.show', compact($precinct));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Precinct  $precinct
     * @return \Illuminate\Http\Response
     */
    public function edit(Precinct $precinct)
    {
        //
        return view('precinct.edit', compact($precinct));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Precinct  $precinct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Precinct $precinct)
    {
        //
        $input=$request->all();
        $precinct->update($input);
        return redirect('/precinct/'.$precinct->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Precinct  $precinct
     * @return \Illuminate\Http\Response
     */
    public function destroy(Precinct $precinct)
    {
        //
        $precinct->delete();
        return redirect('/precinct');
    }
}
