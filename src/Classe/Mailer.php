<?php

namespace App\Classe;
use Mailjet\Resources;
use Mailjet\Client;

class Mailer {
    private $mj;
    public function __construct(){
        $this->mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'],false,['version' => 'v3.1']);
        $this->mj->setSecureProtocol(true);
    }

    public function send($to_email, $to_name, $message){
        $SENDER_EMAIL = $_ENV['ADMIN_EMAIL'];
        $RECIPIENT_EMAIL = $to_email;
        $body = [
          'Messages' => [
              [
                  'From' => [
                      'Email' => "contact@kevin-mercier.fr",
                      'Name' => "Mailjet Pilot"
                  ],
                  'To' => [
                      [
                          'Email' => "$to_email",
                          'Name' => "passenger 1"
                      ]
                  ],
                  'Subject' => "Your email flight plan!",
                  'TextPart' => "Dear passenger 1, welcome to Mailjet! May the delivery force be with you!",
                  'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href=\"https://www.mailjet.com/\">Mailjet</a>!</h3><br />May the delivery force be with you!"
              ]
          ]
      ];
          $response = $this->mj->post(Resources::$Email, ['body' => $body]);
          dd($response->getData());
          $response->success() && var_dump($response->getData());
    }
}