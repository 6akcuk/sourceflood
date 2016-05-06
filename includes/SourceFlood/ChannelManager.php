<?php

namespace SourceFlood;

class ChannelManager
{
	protected static $cache = array(
		'state' => array(
			'name' => null, 
			'channel' => null,
			'links' => array()
		), 
		'city' => array(
			'name' => null, 
			'channel' => null,
			'links' => array()
		)
	);

	public static function create($project_id, $content, $geoData, $type) 
	{
		$channel = new Channel($project_id, $geoData, $type);

		if (self::$cache[$type]['name'] != $geoData[$type]) {
			// Save previous links
			if (self::$cache[$type]['name']) {
				self::save($type);
			}

			// Search for previous channel post
			$founded = $channel->find();
			
			if (!$founded) {
				$channel->create($content, $type == 'city' && self::$cache['state']['channel'] ? self::$cache['state']['channel'] : null);
			}

			self::$cache[$type] = array('name' => $geoData[$type], 'channel' => $channel, 'links' => array());
		}
	}

	public static function getPermalinkStructure($project_id)
	{
		$state = self::$cache['state'];
		$city = self::$cache['city'];

		if ($state['name'] && $city['name']) {
			return array('project_id' => $project_id, 'deep' => 2);
		}
		elseif ($state['name']) {
			return array('project_id' => $project_id, 'deep' => 1, 'type' => 'state');
		}
		elseif ($city['name']) {
			return array('project_id' => $project_id, 'deep' => 1, 'type' => 'city');
		}

		return array();
	}

	public static function addLink($post_id, $geoData) 
	{
		$state = &self::$cache['state'];
		$city = &self::$cache['city'];

		if ($state['name'] && $city['name']) {
			$link = $state['channel']->getLink($city['channel']->getId(), $geoData);
			if (!in_array($link, $state['links']) && $state['channel']->notExists($link)) $state['links'][] = $link;

			$city['links'][] = $city['channel']->getLink($post_id, $geoData);
		}
		elseif ($state['name']) {
			$state['links'][] = $state['channel']->getLink($post_id, $geoData);
		}
		elseif ($city['name']) {
			$city['links'][] = $city['channel']->getLink($post_id, $geoData);
		}
	}

	public static function save($type = null) 
	{
		$state = &self::$cache['state'];
		$city = &self::$cache['city'];

		if ($state['name'] && (!$type || $type == 'state')) {
			$state['channel']->save($state['links']);
			$state['links'] = array();
		}
		if ($city['name'] && (!$type || $type == 'city')) {
			$city['channel']->save($city['links']);
			$city['links'] = array();
		}
	}
}