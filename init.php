<?php
/**
 * Plugin Name: CodeAstrology Webhook for Slack Manager 
 * Plugin URI: https://wooproducttable.com/
 * Description: A A A EDD License Test. A A A EDD License Test. WooCommerce all products display as a table in one page by shortcode. Fully responsive and mobile friendly. Easily customizable - color,background,title,text color etc.
 * Author: Saiful
 * Author URI: https://codeastrology.com/
 * Tags: woocommerce product,woocommerce product table, product table
 * 
 * Version: 1.0.0
 * Requires at least:    4.0.0
 * Tested up to:         6.2
 * WC requires at least: 3.0.0
 * WC tested up to: 	 7.4.0
 * 
 * Text Domain: wpt_pro
 * Domain Path: /languages/
 */

class CA_Webhook_Manage{
    public $site_url;// = "http://wpp.cm/wp-admin/";
    public $webhook_url;
    public $message;
    public function __construct()
    {
        $this->site_url = site_url() . '/wp-admin/';
        $this->webhook_url = 'https://webhook.site/94ef5e18-4ffb-438a-881a-2f5d7d177deb';
        //For Test Workspace 
        $this->webhook_url = 'https://hooks.slack.com/services/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';
        
        //For CodeAstrology Wordspace
        $this->webhook_url = 'https://hooks.slack.com/services/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';



        add_action('fluent_support/ticket_created', [$this,'create_ticket'], 10, 2);

        add_action('fluent_support/response_added_by_agent', [$this,'responsed_added'], 10, 3);
        add_action('fluent_support/response_added_by_customer', [$this,'responsed_added'], 10, 3);
    }
    public function responsed_added($response, $ticket, $customerOrAgent){

        $ticket_id = $ticket['id'] ?? 0;
        $ticket_url = $this->get_ticket_url($ticket_id);

        $priority = $ticket['client_priority'] ?? 'normal';
        $priority = ucwords($priority);

        $person = $response['person'] ?? [];
        $replyed_by = $person['first_name'] ?? '' . ' ' . $person['last_name'] ?? '';

        $title = $ticket['title'] ?? '';

        $content = $response['content'] ?? '';
        $content = strip_tags($content);
        $content = substr($content,0,105) . '...';

        //Others Info:
        $others = [
            // 'privacy' => $ticket['privacy'] ?? '',
            'priority' => $ticket['priority'] ?? '',
            'status' => $ticket['status'] ?? '',
            'reply' => $ticket['response_count'] ?? '',
            // 'last_customer_response' => $ticket['last_customer_response'] ?? '',
            'start' => $ticket['created_at'] ?? '',
            'update' => $ticket['updated_at'] ?? '',
            'closed' => $ticket['closed_by'] ?? '',
        ];

        $other_message ='';
        foreach($others as $other_key => $other){
            if(empty($other)) continue;
            $other_message .= "[{$other_key}:{$other}] ";
        }


        $this->message = "[$replyed_by] has replyed to <$ticket_url|$title>\n\n$content\n\n<$ticket_url|View Ticket>\n\n" . $other_message;

        $this->sendWebHook();


        // $data = [
        //         'response' => $response,
        //         'ticket' => $ticket,
        //         'customerOrAgent' => $customerOrAgent
        //     ];
        // $response = wp_remote_post($this->webhook_url, array(
        //     'body' => json_encode($data),
        //     'headers' => array('Content-Type' => 'application/json'),
        // ));
    
        // if (is_wp_error($response)) {
        //     error_log('Webhook request failed: ' . $response->get_error_message());
        // }
    }
    public function create_ticket($ticket, $customer){
        $ticket_id = $ticket['id'] ?? 0;
        $ticket_url = $this->get_ticket_url($ticket_id);

        $priority = $ticket['client_priority'] ?? 'normal';
        $priority = ucwords($priority);

        $customer_name = $customer['first_name'] ?? '' . ' ' . $customer['last_name'];

        $title = $ticket['title'] ?? '';
        $content = $ticket['content'] ?? '';
        $content = strip_tags($content);
        $content = substr($content,0,105) . '...';


        $this->message = "$priority priority Ticket by <$ticket_url|$customer_name>\n\n <$ticket_url|$title>\n\n$content\n\n<$ticket_url|View Ticket>";

        $this->sendWebHook();
    }

    public function sendWebHook(){
        if(empty($this->message)) return;

        $blocks = array(
            'type'  => 'section',
            'text'  => [
                'type'      => 'mrkdwn',
                // 'text'      => "{$ticket_id} New Paid Time Off request from <example.com|Fred Enriquez>  \n\n<https://example.com|View request>",
                'text'      => $this->message,
            ],
        );

        $data = array(
            'blocks' => [$blocks]
        );
        $response = wp_remote_post($this->webhook_url, array(
            'body' => json_encode($data),
            'headers' => array('Content-Type' => 'application/json'),
        ));
    
        if (is_wp_error($response)) {
            error_log('Webhook request failed: ' . $response->get_error_message());
        }
    }
    public function get_ticket_url($ticket_id){
        return $this->site_url . "admin.php?page=fluent-support#/tickets/$ticket_id/view";
    }
}
$dd = new CA_Webhook_Manage();


// var_dump(33333);
// add_action('fluent_support/ticket_created','webhook_tester', 10, 2);
function webhook_tester($ticket, $customer){
    $site_url = "http://wpp.cm/wp-admin/";
    $webhook_url = 'https://webhook.site/94ef5e18-4ffb-438a-881a-2f5d7d177deb';
    $webhook_url = 'https://hooks.slack.com/services/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';



    $ticket_id = $ticket['id'] ?? 0;
    $ticket_url = $site_url . "admin.php?page=fluent-support#/tickets/$ticket_id/view";

    $priority = $ticket['client_priority'] ?? 'normal';
    $priority = ucwords($priority);

    $message = '';
    $title = $ticket['title'] ?? '';
    $content = $ticket['content'] ?? '';
    $content = substr($content,0,105) . '...';

    //Customer
    $customer_name = $customer['first_name'] ?? '' . ' ' . $customer['last_name'];
    
    $blocks = array(
        'type'  => 'section',
        'text'  => [
            'type'      => 'mrkdwn',
            // 'text'      => "{$ticket_id} New Paid Time Off request from <example.com|Fred Enriquez>  \n\n<https://example.com|View request>",
            'text'      => "$priority priority Ticket by <$ticket_url|$customer_name>\n\n <$ticket_url|$title>\n\n$content\n\n<$ticket_url|View Ticket>",
        ],
    );

    $data = array(
        'blocks' => [$blocks]
    );

    // $data = [
    //     'id' => $post_ID,
    //     'post' => $post,
    //     // 'update' => $update
    // ];

    $response = wp_remote_post($webhook_url, array(
        'body' => json_encode($data),
        'headers' => array('Content-Type' => 'application/json'),
    ));

    if (is_wp_error($response)) {
        error_log('Webhook request failed: ' . $response->get_error_message());
    } else {
        // var_dump("Success");
        // Log the successful webhook request if needed.
        // error_log('Webhook request successful. Response: ' . wp_json_encode($response));
    }
}




/**
{
  "ticket": {
    "title": "With Webhook.site Pro, you get more features lik",
    "content": "With Webhook.site Pro, you get more features likWith Webhook.site Pro, you get more features likWith Webhook.site Pro, you get more features likWith Webhook.site Pro, you get more features likWith Webhook.site Pro, you get more features likWith Webhook.site Pro, you get more features likWith Webhook.site Pro, you get more features likWith Webhook.site Pro, you get more features likWith Webhook.site Pro, you get more features lik",
    "product_id": "",
    "client_priority": "medium",
    "mailbox_id": 1,
    "message_id": "<s3mzhr.1zq1of0ci3gg0@gmail.com>",
    "customer_id": 2,
    "product_source": "local",
    "source": "web",
    "slug": "with-webhook-site-pro-you-get-more-features-lik-1699164207",
    "hash": "a26caf9397",
    "content_hash": "0aca3fa8e5642cf4d47d153282f515f3",
    "last_customer_response": "2023-11-05 06:03:27",
    "created_at": "2023-11-05 06:03:27",
    "updated_at": "2023-11-05T06:03:27+00:00",
    "waiting_since": "2023-11-05 06:03:27",
    "id": 8
  },
  "customer": {
    "id": 2,
    "first_name": "Saiful",
    "last_name": "Islam",
    "email": "testuser@gmail.com",
    "title": null,
    "avatar": null,
    "person_type": "customer",
    "status": "active",
    "ip_address": "::1",
    "last_ip_address": "::1",
    "address_line_1": null,
    "address_line_2": null,
    "city": null,
    "zip": null,
    "state": null,
    "country": null,
    "note": null,
    "hash": "34b83eb66625d555cff34bfd94a521cd",
    "user_id": 11,
    "description": null,
    "remote_uid": null,
    "last_response_at": "2023-11-05 06:02:35",
    "created_at": "2023-11-05T05:49:22+00:00",
    "updated_at": "2023-11-05T06:02:35+00:00",
    "full_name": "Saiful Islam",
    "photo": "http://1.gravatar.com/avatar/?s=96&d=mm&r=g"
  },
  "update": null
}
 */