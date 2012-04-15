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
	protected $id;

	/**
	 * @Column(name="name", type="string")
	 * @var string
	 */
	protected $name;

	/**
	 * @Column(name="ride_date", type="date")
	 */
	protected $date;
	
	/**
	 * @Column(name="start_time", type="time")
	 */
	protected $start_time;
	
	/**
	 * @Column(name="end_time", type="time")
	 */
	protected $end_time;
	
	/**
	 * @Column(name="distance", type="decimal", precision=2)
	 */
	protected $distance;
	
	/**
	 * @Column(name="avg_speed", type="decimal", precision=2)
	 */
	protected $avg_speed;
	
	/**
	 * @Column(name="max_speed", type="decimal", precision=2)
	 */
	protected $max_speed;
	
	/**
	 * @Column(name="avg_cadence", type="integer")
	 */
	protected $avg_cadence;
	
	/**
	 * @Column(name="max_cadence", type="integer")
	 */
	protected $max_cadence;
	
	/**
	 * @Column(name="avg_hr", type="integer")
	 */
	protected $avg_hr;
	
	/**
	 * @Column(name="max_hr", type="integer")
	 */
	protected $max_hr;
	
	/**
	 * @Column(name="climb", type="decimal", precision=2)
	 */
	protected $climb;
	
	/**
	 * @Column(name="description", type="text")
	 * @var string
	 */
	protected $description;

	/**
	 * @OneToMany(targetEntity="CycleGraph\ORM\Entity\Point",
	 * mappedBy="ride", cascade={"all"}, orphanRemoval=true)
	 * @var \CycleGraph\ORM\Entity\Point[]
	 */
	protected $points;

	public function __construct(){
		$this->points = new \Doctrine\Common\Collections\ArrayCollection;
	}
	
	public function GetId() {
		return $this->id;
	}
	
	public function GetName() {
		return $this->name;
	}
	
	public function GetDate() {
		return $this->date;
	}
	
	public function GetTime() {
		
	}
}