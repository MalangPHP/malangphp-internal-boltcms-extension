<?php

namespace Bolt\Extension\MalangPHP\Internal;

use Bolt\BaseExtension;
use Bolt\Extension\Bolt\BoltForms\Event\BoltFormsEvent;
use Bolt\Extension\Bolt\BoltForms\Event\BoltFormsEvents;

/**
 * Class Extension
 * @package Bolt\Extension\MalangPHP\Internal
 */
class Extension extends BaseExtension
{
    /**
     * @var \EventBrite
     */
    private $eventbrite;

    /**
     * initialize extension
     */
    public function initialize()
    {
        require_once "EventBrite/Eventbrite.php";
        $this->eventbrite = new \Eventbrite("P5X5Y2C3I6BFEIB2SW", "1411618625120106952127");

        $this->app["dispatcher"]->addListener(BoltFormsEvents::POST_SUBMIT, [$this, "handleMemberRegistration"]);

        $this->addTwigFunction("get_event", "getEvent");
        $this->addTwigFunction("get_event_attendee", "getEventAttendee");
	    $this->addTwigFunction("md5", "calculateMd5");
    }

    /**
     * @param BoltFormsEvent $event
     */
    public function handleMemberRegistration(BoltFormsEvent $event)
    {
        $form_name = $event->getForm()->getName();

        if ($form_name !== "member")
        {
            return;
        }

        $member = $event->getData();

        $user["email"] = $member["email"];
        $user["roles"] = ['editor'];
        $user["username"] = $member["email"];
        $user["password"] = $member["password"];
        $user["displayname"] = $member["name"];

        $this->app["users"]->saveUser($user);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getEvent($id)
    {
        return $this->eventbrite->event_get(["id" => (string)$id]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getEventAttendee($id)
    {
        return $this->eventbrite->event_list_attendees(["id" => (string)$id]);
    }

    /**
     * @param $string
     * @return string
     */
    public function calculateMd5($string)
    {
        return md5($string);
    }
}
