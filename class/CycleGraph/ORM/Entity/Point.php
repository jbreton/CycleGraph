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
	protected $id;

	/**
	 * @Column(name="real_time", type="datetime")
	 * @var DateTime
	 */
	protected $real_time;
	
	/**
	 * @Column(name="ride_time", type="time")
	 * @var Time
	 */
	protected $ride_time;
	
	/**
	 * @Column(name="lat", type="string")
	 * @var string;
	 */
	protected $lat;
	
	/**
	 * @Column(name="lon", type="string")
	 * @var string;
	 */
	protected $lon;
	
	/**
	 * @Column(name="distance", type="decimal", precision=2)
	 * @var float;
	 */
	protected $distance;
	
	/**
	 * @Column(name="elevation", type="decimal", precision=2)
	 * @var float;
	 */
	protected $elevation;
	
	/**
	 * @Column(name="ascent", type="decimal", precision=2)
	 * @var float;
	 */
	protected $ascent;
	
	/**
	 * @Column(name="descent", type="decimal", precision=2)
	 * @var float;
	 */
	protected $descent;
	
	/**
	 * @Column(name="speed", type="decimal", precision=2)
	 * @var float;
	 */
	protected $speed;
	
	/**
	 * @Column(name="avg_speed", type="decimal", precision=2)
	 * @var float;
	 */
	protected $avg_speed;
	
	/**
	 * @Column(name="cadence", type="integer")
	 * @var integer;
	 */
	protected $cadence;
	
	/**
	 * @Column(name="avg_cadence", type="integer")
	 * @var integer;
	 */
	protected $avg_cadance;
	
	/**
	 * @Column(name="hr", type="integer")
	 * @var integer;
	 */
	protected $hr;
	
	/**
	 * @Column(name="avg_hr", type="integer")
	 * @var integer;
	 */
	protected $avg_hr;
	
	/**
	 * @Column(name="calories", type="integer")
	 * @var integer;
	 */
	protected $calories;
	
	/**
	 * @Column(name="raw_data", type="text")
	 * @var Array;
	 */
	protected $raw_data;
	
	
	/**
	 * @ManyToOne(targetEntity="CycleGraph\ORM\Entity\Ride")
	 * @JoinColumns({
	 *   @JoinColumn(name="ride", referencedColumnName="id")
	 * })
	 */
	protected $ride;
	
	
}
?>