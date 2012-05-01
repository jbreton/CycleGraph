<?php
namespace CycleGraph\ORM\Entity;

/**
 * @Entity
 * @Table(name="ride")
 */
class Ride
{

	/**
	 * @Id
	 * @Column(name="id", type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	public $id;

	/**
	 * @Column(name="ride_date", type="string")
	 */
	public $date;
	
	/**
	 * @Column(name="start_time", type="string")
	 */
	public $startTime;
	
	/**
	 * @Column(name="end_time", type="string")
	 */
	public $endTime;
	
	/**
	 * @Column(name="duration", type="decimal", precision=7, scale=2)
	 */
	public $duration;
	
	/**
	 * @Column(name="stopped_time", type="decimal", precision=7, scale=2)
	 */
	public $stoppedTime;
	
	/**
	 * @Column(name="distance", type="decimal", precision=7, scale=2)
	 */
	public $distance;
	
	/**
	 * @Column(name="avg_speed", type="decimal", precision=7, scale=2)
	 */
	public $avgSpeed;
	
	/**
	 * @Column(name="max_speed", type="decimal", precision=7, scale=2)
	 */
	public $maxSpeed;
	
	/**
	 * @Column(name="avg_cadence", type="integer")
	 */
	public $avgCadence;
	
	/**
	 * @Column(name="max_cadence", type="integer")
	 */
	public $maxCadence;
	
	/**
	 * @Column(name="avg_heart_rate", type="integer")
	 */
	public $avgHeartRate;
	
	/**
	 * @Column(name="max_heart_rate", type="integer")
	 */
	public $maxHeartRate;
	
	/**
	 * @Column(name="calories", type="integer")
	 */
	public $calories;
	
	/**
	 * @Column(name="ascent", type="decimal", precision=7, scale=2)
	 */
	public $ascent;
	
	/**
	 * @Column(name="descent", type="decimal", precision=7, scale=2)
	 */
	public $descent;
	
	/**
	 * @Column(name="description", type="text")
	 * @var string
	 */
	public $description = '';
	
	/**
	 * @Column(name="ignore_ride", type="text");
	 */
	public $ignore = "N";
	
	/**
	 * @ManyToOne(targetEntity="CycleGraph\ORM\Entity\Route")
	 * @JoinColumns({
	 *   @JoinColumn(name="route", referencedColumnName="id")
	 * })
	 */
	public $route;

	/**
	 * @OneToMany(targetEntity="CycleGraph\ORM\Entity\Point",
	 * mappedBy="ride", cascade={"all"}, orphanRemoval=true)
	 * @var \CycleGraph\ORM\Entity\Point[]
	 */
	public $points;

	public function __construct(){
		$this->points = new \Doctrine\Common\Collections\ArrayCollection;
	}
}