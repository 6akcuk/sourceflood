<?php

namespace SourceFlood\Models;

class Shortcode extends AbstractModel
{
	public $fillable = array('shortcode', 'type', 'content');

	public function getStatic($limit = 20) 
	{
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}{$this->table} WHERE type = 'static' LIMIT %d, %d", [$this->offset, $limit]);
		return $wpdb->get_results($sql);
	}

	public function countStatic() 
	{
		global $wpdb;

		$row = $wpdb->get_row("SELECT COUNT(*) AS total FROM {$wpdb->prefix}{$this->table} WHERE type = 'static'");
		return $row->total;
	}

	public function getDynamic($limit = 20) 
	{
		global $wpdb;

		$sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}{$this->table} WHERE type = 'dynamic' LIMIT %d, %d", [$this->offset, $limit]);
		return $wpdb->get_results($sql);
	}

	public function countDynamic() 
	{
		global $wpdb;

		$row = $wpdb->get_row("SELECT COUNT(*) AS total FROM {$wpdb->prefix}{$this->table} WHERE type = 'dynamic'");
		return $row->total;
	}
}