<?php 

namespace Flutterwave;

class Flutterwave
{
    var $return_url = 'site/user/verify_payment';
    var $public_key = "FLWPUBK-261eaf30c3790-X";    // public key
    var $secret_key = "FLWSECK_TEST-ade60954-X";    // secret key

    

    /**You can also Move to your controller from here if you intend to use as Controller. */

    public function bankTransfer()
    {
        $post_data = [
            'account_bank' => '044',
            'account_number' => '0690000040',
            'amount' => 5500,
            'narration' => 'Akhlm Pstmn Trnsfr xx007',
            'currency' => 'NGN',
            'reference' => 'akhlm-pstmnpyt-rfxx007_PMCKDU_1',
            'callback_url' => $this->return_url,
            'debit_currency' => 'NGN',
          ];
        $endpoint = 'transfers';
        $init = $this->Flutterwave->curl_post($endpoint, $post_data);
        print_r($init);
    }

    public function create_card()
    {
        $post_data = [
            'account_bank' => '044',
            'account_number' => '0690000040',
            'amount' => 5500,
            'narration' => 'Akhlm Pstmn Trnsfr xx007',
            'currency' => 'NGN',
            'reference' => 'akhlm-pstmnpyt-rfxx007_PMCKDU_1',
            'callback_url' => $this->return_url,
            'debit_currency' => 'NGN',
          ];
        $endpoint = 'virtual-cards';
        $init = $this->Flutterwave->curl_post($endpoint, $post_data);
        print_r($init);
    }

    /**
     * This will return an array of all created virtual cards
     * mainly to be used for admin menu
     */
    public function get_cards()
    {
        $endpoint = 'virtual-cards';
        $init = $this->Flutterwave->curl_get($endpoint);
        print_r($init);
    }

    /**
     * This will return information regarding a single card.
     * can be used by both admin and user.
     */
    public function get_single_card()
    {
        $card_id = $_GET['card'];
        $endpoint = "virtual-cards/$card_id";
        $init = $this->Flutterwave->curl_get($endpoint);
        print_r($init);
    }

    public function get_card_transactions()
    {
        $card_id = $_GET['card'];
        $endpoint = "virtual-cards/$card_id/transactions";
        $init = $this->Flutterwave->curl_get($endpoint);
        print_r($init);
    }

    public function card_status()
    {
        $card_id = $_GET['card'];
        $action = $_GET['action']; // action can be 'block' or 'unblock'
        $endpoint = "virtual-cards/$card_id/status/status_action";
        $init = $this->Flutterwave->curl_put($endpoint);
        print_r($init);
    }

    /**
     * Terminate card will delete the card.
     */

    public function terminate_card()
    {
        $card_id = $_GET['card'];
        $endpoint = "virtual-cards/$card_id/terminate";
        $init = $this->Flutterwave->curl_put($endpoint);
        print_r($init);
    }

    public function fund_card()
    {
        $post_data = [
            'debit_currency' => 'NGN',
            'amount' => 400000,
        ];
        $card_id = $_GET['card'];
        $endpoint = "virtual-cards/$card_id/fund";
        $init = $this->Flutterwave->curl_post($endpoint, $post_data);
        print_r($init);
    }

    /**
     * Get list of banks and banks code to create bank transfer.
     */
    public function get_banks()
    {
        $country = $_GET['country']; // ex: NG, GH, KE, UG, ZA or TZ.
        $endpoint = "banks/$country";
        $init = $this->Flutterwave->curl_get($endpoint);
        print_r($init);
    }

    public function get_transfers()
    {
        $endpoint = 'transfers';
        $init = $this->Flutterwave->curl_get($endpoint);
        print_r($init);
    }

    function verify_transaction($payment_id)
    {
        $endpoint = "transactions/$payment_id/verify";
        $init = $this->curl_get($endpoint); // please verify payment yourself using the $init['data']['charged_amount'] and confirm if payment as not been validated before inserting into ::DB
        return $init;
    }

    /**
     * The below function is responsible for initiate a fresh payment to the 
     * flutterwave payment gateway.
     */
    public function initiate_payment()
    {
        $post_data = [
            'tx_ref' => 'hooli-tx-1920bbtytty', // unique transaction reference code
            'amount' => '100',
            'currency' => 'NGN',
            'redirect_url' => $this->return_url,
            'payment_options' => 'card',
            'meta' => [
              'consumer_id' => 23,
              'consumer_mac' => '92a3-912ba-1192a',
            ],
            'customer' => [
              'email' => 'user@gmail.com',
              'phonenumber' => '080****4528',
              'name' => 'Yemi Desola',
            ],
            'customizations' => [
              'title' => 'Pied Piper Payments',
              'description' => 'Middleout isn\'t free. Pay the price',
              'logo' => 'https://assets.piedpiper.com/logo.png',
            ],
        ];
        $endpoint = "payments";
        $init = $this->Flutterwave->curl_post($endpoint, $post_data); 
        return $init;
    }

    public function curl_post($endpoint, $post_data)
    {
        $url = "https://api.flutterwave.com/v3/";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->secret_key,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($response, true);
        return $resp;
    }

    public function curl_get($endpoint)
    {
        $url = "https://api.flutterwave.com/v3/";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.$endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->secret_key,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($response, true);
        return $resp;
    }
    
    public function curl_put($endpoint)
    {
        $url = "https://api.flutterwave.com/v3/";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.$endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->secret_key,
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $resp = json_decode($response, true);
        return $resp;
    }
}
