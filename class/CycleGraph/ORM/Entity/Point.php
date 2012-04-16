<?php
namespace CycleGraph\ORM\Entity;

/**
 * @Entity
 * @Table(name="point")
 */
class Point
{
	/**
	 * @Id
	 * @Column(name="id", type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	public $id;

	/**
	 * @Column(name="real_time", type="string")
	 * @var string
	 */
	public $real_time;
	
	/**
	 * @Column(name="ride_time", type="string")
	 * @var string
	 */
	public $ride_time;
	
	/**
	 * @Column(name="latitude", type="string")
	 * @var string;
	 */
	public $latitude;
	
	/**
	 * @Column(name="longitude", type="string")
	 * @var string;
	 */
	public $longitude;
	
	/**
	 * @Column(name="distance", type="decimal", precision=7, scale=2)
	 * @var float;
	 */
	public $distance;
	
	/**
	 * @Column(name="elevation", type="decimal", precision=7, scale=2)
	 * @var float;
	 */
	public $elevation;
	
	/**
	 * @Column(name="ascent", type="decimal", precision=7, scale=2)
	 * @var float;
	 */
	public $ascent;
	
	/**
	 * @Column(name="descent", type="decimal", precision=7, scale=2)
	 * @var float;
	 */
	public $descent;
	
	/**
	 * @Column(name="speed", type="decimal", precision=7, scale=2)
	 * @var float;
	 */
	public $speed;
	
	/**
	 * @Column(name="avg_speed", type="decimal", precision=7, scale=2)
	 * @var float;
	 */
	public $avg_speed;
	
	/**
	 * @Column(name="cadence", type="integer")
	 * @var integer;
	 */
	public $cadence;
	
	/**
	 * @Column(name="avg_cadence", type="integer")
	 * @var integer;
	 */
	public $avg_cadance;
	
	/**
	 * @Column(name="hr", type="integer")
	 * @var integer;
	 */
	public $hr;
	
	/**
	 * @Column(name="avg_hr", type="integer")
	 * @var integer;
	 */
	public $avg_hr;
	
	/**
	 * @Column(name="calories", type="integer")
	 * @var integer;
	 */
	public $calories;
	
	/**
	 * @Column(name="raw_data", type="text")
	 * @var Array;
	 */
	public $raw_data;
	
	
	/**
	 * @ManyToOne(targetEntity="CycleGraph\ORM\Entity\Ride")
	 * @JoinColumns({
	 *   @JoinColumn(name="ride", referencedColumnName="id")
	 * })
	 */
	public $ride;	
}