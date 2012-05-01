<?php

namespace CycleGraph\ORM\Entity;

/**
 * @Entity 
 * @Table(name="route")
 */
class Route {
	
	/**
	 * @Id
	 * @Column(name="id", type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	public $id;
	
	/**
	 * @Column(name="start_time", type="string")
	 */
	public $name;
	
	/**
	 * @Column(name="description", type="text")
	 */
	public $description;
	
	/**
	 * @OneToMany(targetEntity="CycleGraph\ORM\Entity\Ride",
	 * mappedBy="route", cascade={"all"}, orphanRemoval=true)
	 * @var \CycleGraph\ORM\Entity\Ride[]
	 */
	public $rides;
	
	/**
     * @OneToOne(targetEntity="CycleGraph\ORM\Entity\Ride")
     * @JoinColumn(name="fastest_ride", referencedColumnName="id")
     */
	public $fastestRide;
	
	/**
     * @OneToOne(targetEntity="CycleGraph\ORM\Entity\Ride")
     * @JoinColumn(name="median_ride", referencedColumnName="id")
     */
	public $medianRide;
	
	/**
     * @OneToOne(targetEntity="CycleGraph\ORM\Entity\Ride")
     * @JoinColumn(name="slowest_ride", referencedColumnName="id")
     */
	public $slowestRide;
	
	/**
	 * @Column(name="total_distance", type="decimal", precision=9, scale=2)
	 */
	public $totalDistance;
	
	/**
	 * @Column(name="total_duration", type="decimal", precision=9, scale=2)
	 */
	public $totalDuration;
	
	/**
	 * @Column(name="max_speed", type="decimal", precision=7, scale=2)
	 */
	public $maxSpeed;
	
	/**
	 * @Column(name="avg_speed", type="decimal", precision=7, scale=2)
	 */
	public $avgSpeed;
	
	/**
	 * @Column(name="total_calories", type="integer")
	 */
	public $totalCalories;
	
	/**
	 * @Column(name="total_ascent", type="integer")
	 */
	public $totalAscent;
	
	/**
	 * @Column(name="total_descent", type="integer")
	 */
	public $totalDescent;
	
	public function __construct(){
		$this->rides = new \Doctrine\Common\Collections\ArrayCollection;
	}
	
	public function updateStatistics() {
		$fastestTime = PHP_INT_MAX;
		$slowestTime = 0;
		$this->totalDistance = 0;
		$this->totalDuration = 0;
		$this->maxSpeed = 0;
		$totalAvgSpeed = 0;
		$this->totalCalories = 0;
		$this->totalAscent = 0;
		$this->totalDescent = 0;
		
		$ridesByDuration = array();
		
		foreach($this->rides as $index => $ride) {
			echo $index."\n";
			$ridesByDuration[$ride->duration] = $index;
			
			if($ride->duration < $fastestTime) {
				$fastestTime = $ride->duration;
				$this->fastestRide = $ride;
			}
			
			if($ride->duration > $slowestTime) {
				$slowestTime = $ride->duration;
				$this->slowestRide = $ride;
			}
			
			$this->totalDuration += $ride->duration;
			$this->totalDistance += $ride->distance;
			$this->maxSpeed = max($this->maxSpeed, $ride->maxSpeed);
			$totalAvgSpeed += $ride->avgSpeed;
			$this->totalCalories += $ride->calories;
			$this->totalAscent += $ride->ascent;
			$this->totalDescent += $ride->descent;
		}
		
		ksort($ridesByDuration);
		$durations = array_values($ridesByDuration);
		$median = (count($durations) % 2 == 1 ? $durations[(count($durations)+1)/2 - 1] : $durations[count($durations)/2-1]);
		$this->medianRide = $this->rides[$median];
		
		$this->avgSpeed = $totalAvgSpeed / count($this->rides);
	}
}