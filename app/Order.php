<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use \DiscordWebhooks\Client;
use \DiscordWebhooks\Embed;

class Order extends Model
{
     public function lastIPN(){
    	return(json_decode($this->postdata,true));
    }

    public function orderNotification()
    {
        if(\Setting::get('DISCORD_ORDER_ENABLED', false)){
            $webhook = \Setting::get('DISCORD_ORDER_WEBHOOK');

            $webhook = new Client($webhook);
            $embed = new Embed();
            $embed->color("1376020");
            $embed->description($this->username.' has just donated '.$this->lastIPN()["payment_gross"]." ".$this->lastIPN()["mc_currency"]);
            $webhook->embed($embed)->send();
        }

        //\Mail::to($this->lastIPN()['payer_email'])->send(new \App\Mail\OrderProcessed($this));
    }
    public static function paymentsDays($days)
    {
        return Order::moneyReceived(\Carbon\Carbon::now()->subDays($days), \Carbon\Carbon::now()->addDays(1));
    }

   public static function topUsernameDays($days){
    	$db = Order::between(\Carbon\Carbon::now()->subDays($days), \Carbon\Carbon::now()->addDays(1),false)->select(DB::raw("`username`,COUNT(`username`) AS `username_occurrence`"))->groupBy('username')->orderByRaw("`username_occurrence` DESC")->get();
    	if($db->first() != null){
    		return $db;
    	}else{
    		return "None";
    	}

    }
    public static function topUsernames($start,$end){
    	return Order::between($start,$end,false)->select(DB::raw("`username`,COUNT(`username`) AS `username_occurrence`"))->groupBy('username')->orderByRaw("`username_occurrence` DESC")->get();

    }
  public static function moneyReceivedDays($days){
		return Order::moneyReceived(\Carbon\Carbon::now()->subDays($days), \Carbon\Carbon::now()->addDays(1));
    }

    public static function moneyReceived($start, $end){
		return Order::between($start,$end,false)->select(DB::raw("SUM(`total`) AS Total"))->get()->first()->Total;
    }

    public static function lastDays($days)
    {
        return Order::between(\Carbon\Carbon::now()->subDays($days), \Carbon\Carbon::now()->addDays(1));
    }

    public static function between($start, $end,$get = true)
    {
    	if($get){
        	return \DB::table('orders')->whereBetween('created_at', [$start,$end])->get();
    	}else{
			return \DB::table('orders')->whereBetween('created_at', [$start,$end]);
    	}
    }


}
