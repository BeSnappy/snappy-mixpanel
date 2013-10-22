<?php namespace Snappy\Apps\Mixpanel;

use Snappy\Apps\App as BaseApp;
use Snappy\Apps\ContactCreatedHandler;
use Snappy\Apps\IncomingMessageHandler;

class App extends BaseApp implements ContactCreatedHandler, IncomingMessageHandler {

	/**
	 * The name of the application.
	 *
	 * @var string
	 */
	public $name = 'Mixpanel';

	/**
	 * The application description.
	 *
	 * @var string
	 */
	public $description = 'Send metrics to Mixpanel';

	/**
	 * Any notes about this application
	 *
	 * @var string
	 */
	public $notes = '';

	/**
	 * The application's icon filename.
	 *
	 * @var string
	 */
	public $icon = 'mixpanel.png';

	/**
	 * The application service's main website.
	 *
	 * @var string
	 */
	public $website = 'https://mixpanel.com';

	/**
	 * The application author name.
	 *
	 * @var string
	 */
	public $author = 'UserScape, Inc.';

	/**
	 * The application author e-mail.
	 *
	 * @var string
	 */
	public $email = 'it@userscape.com';

	/**
	 * The settings required by the application.
	 *
	 * @var array
	 */
	public $settings = array(
		array('name' => 'token', 'type' => 'text', 'help' => 'Enter your API Token', 'validate' => 'required'),
		array('name' => 'event', 'placeholder' => 'Support Request', 'type' => 'text', 'help' => 'Mixpanel event name for new requests', 'validate' => 'required'),
	);

	/**
	 * Add the contact
	 *
	 * @param  array  $ticket
	 * @param  array  $contact
	 * @return void
	 */
	public function handleContactCreated(array $ticket, array $contact)
	{
		$mixpanel = $this->getClient();
		$mixpanel->people->set($contact['value'], array(
			'$first_name'       => $contact['first_name'],
			'$last_name'        => $contact['last_name'],
			'$email'            => $contact['value'],
		));
	}

	/**
	 * Track an incoming message
	 *
	 * @param  array  $message
	 * @return void
	 */
	public function handleIncomingMessage(array $message)
	{
		$mixpanel = $this->getClient();
		$mixpanel->track($this->config['event'], array(
			'distinct_id' => $message['creator']['value'],
			'ticket' => $message['ticket_id'],
			'name' => $message['creator']['first_name']. ' ' . $message['creator']['last_name'],
			'email' => $message['creator']['value'],
		));
	}

	/**
	 * Send the actual request
	 *
	 * @param  string $action The api action
	 * @param  array  $data   Array of properties to sent to the api
	 * @return void
	 */
	protected function getClient()
	{
		return \Mixpanel::getInstance($this->config['token']);
	}
}
