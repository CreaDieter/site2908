<?php
class Website_Service_Newsletter {
	protected $url;
	protected $params;
	protected $list;

	public function __construct() {

		throw new Exception('Include packo credentials in newsletter system');

		// By default, this sample code is designed to get the result from your
		// server (where Studio Emma e-news Studio is installed) and to print out the result
		$this->url    = 'http://enews.vlam.be';
		$this->list   = 32;

		$this->params = array(

			// the API Username and Password are the same as your login access to the Admin panel
			// replace these with your info
			'api_user'     => 'admin',
			'api_pass'     => 'Stud1oemma',

			// this is the action that adds a message
			//'api_action'   => 'message_add',

			// define the type of output you wish to get back
			// possible values:
			// - 'xml'  :      you have to write your own XML parser
			// - 'json' :      data is returned in JSON format and can be decoded with
			//                 json_decode() function (included in PHP since 5.2.0)
			// - 'serialize' : data is returned in a serialized format and can be decoded with
			//                 a native unserialize() function
			'api_output'   => 'serialize',
		);
	}


	protected function performCall($post,$action) {
		// Set the action
		$this->params['api_action'] = $action;

		// This section takes the input fields and converts them to the proper format
		$query = "";
		foreach( $this->params as $key => $value ) $query .= $key . '=' . urlencode($value) . '&';
		$query = rtrim($query, '& ');

		// This section takes the input data and converts it to the proper format
		$data = "";
		foreach( $post as $key => $value ) $data .= $key . '=' . urlencode($value) . '&';
		$data = rtrim($data, '& ');

		// clean up the url
		$url = rtrim($this->url, '/ ');

		// define a final API request - GET
		$api = $url . '/admin/api.php?' . $query;


		$request = curl_init($api); // initiate curl object
		curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
		curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
		//curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS

		$response = (string)curl_exec($request); // execute curl post and store results in $response

		// additional options may be required depending upon your server configuration
		// you can find documentation on curl options at http://www.php.net/curl_setopt
			curl_close($request); // close curl object

		if ( !$response ) {
			die('Nothing was returned. Do you have a connection to Email Marketing server?');
		}

		// This line takes the response and breaks it into an array using:
		// JSON decoder
		//$result = json_decode($response);
		// unserializer
				$result = unserialize($response);
		// XML parser...
		// ...

		return $result;
	}

	public function addMessage($data) {
		// here we define the data we are posting in order to perform an update
		$post = array(
			//'id'                     => 0, // adds a new one
			'format'                   => 'mime', // possible values: html, text, mime (both html and text)
			'subject'                  => $data['subject'],
			'fromemail'                => $data['from_email'],
			'fromname'                 => $data['from_name'],
			//'reply2'                   => 'reply2@example.com',
			'priority'                 => '3', // 1=high, 3=medium/default, 5=low
			'charset'                  => 'utf-8',
			'encoding'                 => 'quoted-printable',

			// html version
			'htmlconstructor'          => 'editor', // possible values: editor, external, upload
			// if editor, it uses 'html' parameter
			// if external, uses 'htmlfetch' and 'htmlfetchwhen' parameters
			// if upload, uses 'message_upload_html'
			'html'                     => $data['content_html'], // content of your html email
			//'htmlfetch'                => 'http://yoursite.com', // URL where to fetch the body from
			//'htmlfetchwhen'            => 'send', // possible values: (fetch at) 'send' and (fetch) 'pers'(onalized)
			//'message_upload_html[]'  => 123, // not supported yet: an ID of an uploaded content

			// text version
			'textconstructor'          => 'editor', // possible values: editor, external, upload
			// if editor, it uses 'text' parameter
			// if external, uses 'textfetch' and 'textfetchwhen' parameters
			// if upload, uses 'message_upload_text'
			'text'                     => $data['content_text'], // content of your text only email
			//'textfetch'                => 'http://yoursite.com', // URL where to fetch the body from
			//'textfetchwhen'            => 'send', // possible values: (fetch at) 'send' and (fetch) 'pers'(onalized)
			//'message_upload_text[]'  => 123, // not supported yet: an ID of an uploaded content


			// assign to lists:
			'p[1]'                   => $this->list, // example list ID
			//'p[345]'                 => 345, // some additional lists?
		);

		return $this->performCall($post,'message_add');
	}

	public function addCampaign($data) {

		$post = array(
			//'id'                      => 0, // adds a new one

			'type'                      => 'single', // campaign type (defaults to single)
			// 'single', 'recurring', 'split', 'responder', 'reminder', 'special', 'activerss', 'text'

			'filterid'                  => 0, // use list segment with ID (0 for no segment),
			'bounceid'                  => -1,
			// -1: use all available bounce accounts, 0: don't use bounce management, or ID of a bounce account

			'name'                      => $data['name'],
			'sdate'                     => $data['send_date'], // the date when campaign should be sent out
			// not used for 'responder', 'reminder', 'special'

			'status'                    => 0, // 0: draft, 1: scheduled
			'public'                    => 1, // if campaign should be visible via public side

			//'mailer_log_file'         => 4, // turn on logging for this campaign (will be stored in /cache/ folder)

			'tracklinks'                => 'all', // possible values: 'all', 'mime', 'html', 'text', 'none'
			// setting this value to all will let the system know to fetch, parse, and track all emails it finds in both TEXT and HTML body
			// if mime/html/text is provided, then variable 'links' also must exist, with a list of URLs to track
			// choosing html or text will track only the links in that message body

			//'tracklinksanalytics'     => 1, // set to 1 if you wish to use list's Google Analytics settings

			'trackreads'                => 1, // possible values: 0 and 1

			'trackreadsanalytics'     => 0, // set to 1 if you wish to use list's Google Analytics settings
			//'analytics_campaign_name' => '', // set the name of this campaign to use in Google Analytics

			//'tweet'                   => 1, // set to 1 if you wish to use list's Twitter settings
			//'facebook'                => 1, // set to 1 if you wish to use list's Facebook settings

			//'embed_images'            => 1, // uncomment this line if you wish to embed images

			'htmlunsub'                 => 1, // append unsubscribe link to the bottom of HTML body
			'textunsub'                 => 1, // append unsubscribe link to the bottom of TEXT body

			// provide custom unsubscribe link addons
			//'htmlunsubdata'           => '<div><a href="%UNSUBSCRIBELINK%">Click here</a> to unsubscribe from future mailings.</div>', // (DOWNLOADED USERS ONLY)
			//'textunsubdata'           => 'Click here to unsubscribe from future mailings: %UNSUBSCRIBELINK%', // (DOWNLOADED USERS ONLY)

			/* IF RECURRING MAILING */
			//'recurring'               => 'day1', // repeat every day
			// possible values are day1, day2, week1, week2, month1, month2, quarter1, quarter2, year1, year2
			// values ending with 1 mean "every", and ending with 2 mean "every other"

			/* IF SPLIT MAILING */
			//'split_type'              => 'even', // send each message to even number of subscribers
			// possible values are even, read and click. if read or click is used, 'split_offset' and 'split_offset_type' need to be present.
			// in that case it will use a "winner" scenario
			//'split_offset'            => 12, // how much to wait
			//'split_offset_type'       => 'hour', // how long to wait. possible values: hour, day, week, month

			/* IF AUTO-RESPONDER */
			//'responder_offset'        => 12, // how long after (un)subscription to send it
			//'responder_type'          => 'subscribe', // after what to send it. possible values are: subscribe and unsubscribe

			/* IF AUTO-REMINDER */
			//'reminder_field'          => 12, // what subscriber field to use. possible values are cdate, sdate, udate, or an ID of a custom field
			//'reminder_format'         => 'yyyy-mm-dd', // format in which the date is represented in abovementioned subscriber field
			//'reminder_type'           => 'month_day', // match just a month and the day (yearly), or match year as well.
			//possible values: month_day, year_month_day
			//'reminder_offset'         => 5, // how many days/weeks/months/years from that date should it trigger
			//'reminder_offset_type'    => 'day', // possible values: day, week, month, year
			//'reminder_offset_sign'    => '+', // possible values: + and -.
			// in this case it would be: +5days from today needs to be the value of subscriber's field

			/* IF ACTIVERSS */
			//'activerss_interval'      => 'day1', // same options as for recurring mailings


			// send to lists:
			'p[1]'                       => $this->list, // example list ID
			//'p[1]'                     => 345, // some additional lists?

			// send message(s):
			'm[70]'                    => $data['messageId'], // example message ID would be 123. 100 means send to 100% of subscribers
			/* IF SPLIT MAILING */
			// if sending a split mailing with "winner" scenario, more than one message can be provided.
			// in that case, the sum of all messages should total to under 100%
			// (so the rest of subscribers can receive a winner message after it is determined)
			//'r[453643]'               => 10, // some additional messages?
			//'r[346146]'               => 10, // some additional messages?



			// if 'tracklinks' variable is not set to 'all', provide a list of links to track here

			// tracked link example
			//'linkurl[0]'               => 'http://www.google.com/',
			//'linkname[0]'              => 'Google Inc.',
			//'linkmessage[0]'              => 123, // found in message with this ID

			// more tracked links...
			//'linkurl[1]'               => 'http://www.yahoo.com/',
			//'linkname[1]'              => 'Yahoo Inc.',
			//'linkmessage[1]'              => 345, // found in message with this ID
		);

		return $this->performCall($post,'campaign_create');
	}

	public function addMessageTemplate($data) {
		$post = array(
			'name'                     => 'RVBDB', // internal template name
			'subject'                  => 'Recht Van Bij De Boer', // template subject
			'html'                     => $data['content_html'],
			'template_scope'           => 'all', // list visibility: 'all' or 'specific'
			'p[1]'                     => '1', // if template_scope = 'specific', supply individual list ID's
		);

		return $this->performCall($post,'message_template_add');
	}

    public function addSubscriber($data) {
        // Test the list
        //$this->list = 55;

        // here we define the data we are posting in order to perform an update
        $post = array(
            //'id'                     => 0, // adds a new one
            //'username'               => $params['api_user'], // username cannot be changed!
            'email'                    => $data,
            //'first_name'               => 'FirstName',
            //'last_name'                => 'LastName',

            // any custom fields
            //'field[345,0]'           => 'field value', // where 345 is the field ID

            // assign to lists:
            'p['.$this->list.']'                   => $this->list, // example list ID
            'status['.$this->list.']'              => 1, // 0: unconfirmed (Downloaded users only), 1: active, 2: unsubscribed
            //'form'                                     => 1001, // Subscription Form ID, to inherit those redirection settings
            //'noresponders[123]'      => 1, // uncomment to set "do not send any future responders"
            //'sdate[123]'             => '2009-12-07 06:00:00', // Subscribe date for particular list - leave out to use current date/time
            // use the folowing only if status=0
            //'sendoptin[123]'         => 1, // uncomment to send an opt-in confirmation email
            // use the folowing only if status=1
            'instantresponders['.$this->list.']' => 0, // set to 0 to if you don't want to sent instant autoresponders
            //'lastmessage[123]'       => 1, // uncomment to set "send the last broadcast campaign"

            //'p[]'                    => 345, // some additional lists?
            //'status[345]'            => 1, // some additional lists?
        );

        return $this->performCall($post,'subscriber_add');
    }
}