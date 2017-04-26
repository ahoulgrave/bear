<?php
namespace Untitled\Event;

/**
 * Class PreDispatchEvent
 *
 * @package Untitled\Event
 */
class PreDispatchEvent extends RequestAwareEvent
{
    const EVENT_NAME = 'untitled.predispatch';
}
