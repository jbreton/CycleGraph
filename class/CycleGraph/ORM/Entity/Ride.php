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
	 * @Column(name="name", type="string")
	 * @var string
	 */
	public $name;

	/**
	 * @Column(name="ride_date", type="string")
	 */
	public $date;
	
	/**
	 * @Column(name="start_time", type="string")
	 */
	public $start_time;
	
	/**
	 * @Column(name="end_time", type="string")
	 */
	public $end_time;
	
	/**
	 * @Column(name="distance", type="decimal", precision=7, scale=2)
	 */
	public $distance;
	
	/**
	 * @Column(name="avg_speed", type="decimal", precision=7, scale=2)
	 */
	public $avg_speed;
	
	/**
	 * @Column(name="max_speed", type="decimal", precision=7, scale=2)
	 */
	public $max_speed;
	
	/**
	 * @Column(name="avg_cadence", type="integer")
	 */
	public $avg_cadence;
	
	/**
	 * @Column(name="max_cadence", type="integer")
	 */
	public $max_cadence;
	
	/**
	 * @Column(name="avg_hr", type="integer")
	 */
	public $avg_hr;
	
	/**
	 * @Column(name="max_hr", type="integer")
	 */
	public $max_hr;
	
	/**
	 * @Column(name="ascent", type="decimal", precision=7, scale=2)
	 */
	public $ascent;
	
	/**
	 * @Column(name="description", type="text")
	 * @var string
	 */
	public $description;

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