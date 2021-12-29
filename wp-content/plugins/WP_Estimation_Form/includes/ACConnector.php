<?php

class ACConnector
{
    private $API_URL = 'https://intertech31785.api-us1.com/api/3/';
    private $API_KEY = '69c99a96086ad5552e9652a4e9432a2381542022e8bf3dda22c9b801e5664cc261a47ca6';
    private $LIST_ID;

    function __construct($list_id)
    {
        $this->LIST_ID = $list_id;
    }

    public function call($method, $url, $payload = null)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "{$this->API_URL}{$url}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Api-Token: {$this->API_KEY}"
            ]
        ]);

        if (!empty($payload)) {
            $data = json_encode($payload);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return json_decode($response);
        }
    }

    public function upsertContact($payload)
    {
        return self::call('POST', 'contact/sync', [
            "contact" => [
                "email" => $payload['email'],
                "firstName" => $payload['firstName'],
                "lastName" => $payload['lastName'],
                "phone" => $payload['phone'],
                "fieldValues" => [
                    [
                        "field" => "1",
                        "value" => self::get_property($payload, 'zip')
                    ],
                    [
                        "field" => "2",
                        "value" => self::get_property($payload, 'town')
                    ],
                    [
                        "field" => "3",
                        "value" => self::get_property($payload, 'address')
                    ],
                    [
                        "field" => "4",
                        "value" => self::get_property($payload, 'note')
                    ],
                    [
                        "field" => "5",
                        "value" => self::get_property($payload, 'propertyType')
                    ],
                    [
                        "field" => "6",
                        "value" => self::get_property($payload, 'supportUsage')
                    ],
                    [
                        "field" => "7",
                        "value" => self::get_property($payload, 'hasThreePhase')
                    ],
                    [
                        "field" => "8",
                        "value" => self::get_property($payload, 'roof')
                    ],
                    [
                        "field" => "9",
                        "value" => self::get_property($payload, 'electricityBill')
                    ],
                    [
                        "field" => "10",
                        "value" => self::get_property($payload, 'roofGeneral')
                    ],
                    [
                        "field" => "11",
                        "value" => self::get_property($payload, 'hasThreePhaseGeneral')
                    ],
                    [
                        "field" => "12",
                        "value" => self::get_property($payload, 'electricityBillGeneral')
                    ],
                    [
                        "field" => "13",
                        "value" => self::get_property($payload, 'investDateGeneral')
                    ]
                ]
            ]
        ]);
    }

    private function get_property($data, $property)
    {
        if (!empty($data[$property]) && $data[$property] != '') {
            return $data[$property];
        } else {
            return '';
        }
    }

    public function subscribeContactToList($contactId)
    {
        return self::call('POST', 'contactLists', [
            "contactList" => [
                "list" => $this->LIST_ID,
                "contact" => $contactId,
                "status" => 1
            ]
        ]);
    }

    public function submitForm($formData)
    {
        $user = self::upsertContact($formData);

        if (!empty($user->contact)) {
            return self::subscribeContactToList($user->contact->id);
        } else {
            return $user;
        }
    }
}