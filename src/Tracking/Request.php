<?php

namespace TaivasAPM\Tracking;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * This is a data object which holds information about a request
 * and provides some functionality for extracting relevant
 * information from the request
 *
 * Class Request
 * @package TaivasAPM\Tracking
 */
class Request {
    private $url = null;
    private $started_at = null;
    private $stopped_at = null;
    private $queries = null;
    private $max_memory = null;

    /**
     * @return null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param null $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return null
     */
    public function getStartedAt()
    {
        return $this->started_at;
    }

    /**
     * @param null $started_at
     */
    public function setStartedAt($started_at): void
    {
        $this->started_at = $started_at;

    }

    /**
     * @return null
     */
    public function getStoppedAt()
    {
        return $this->stopped_at;
    }

    /**
     * @param null $stopped_at
     */
    public function setStoppedAt($stopped_at): void
    {
        $this->stopped_at = $stopped_at;
    }

    /**
     * @return null
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param null $queries
     */
    public function setQueries($queries): void
    {
        $this->queries = $queries;
    }

    /**
     * @return null
     */
    public function getMaxMemory()
    {
        return $this->max_memory;
    }

    /**
     * @param null $max_memory
     */
    public function setMaxMemory($max_memory): void
    {
        $this->max_memory = $max_memory;
    }

    /**
     * Returns the amount of milliseconds the total request took
     *
     * @return float|null
     */
    public function getRequestDuration() {
        if(!$this->started_at || !$this->stopped_at) {
            return null;
        }
        return $this->stopped_at - $this->started_at;
    }

    /**
     * Returns the amount of milliseconds the database queries took
     *
     * @return float|null
     */
    public function getDBDuration() {
        if($this->queries === null) {
            return null;
        }
        return collect($this->queries)->sum('time');
    }

    /**
     * Returns the amount queries which were executed
     *
     * @return float|null
     */
    public function getDBCount() {
        if($this->queries === null) {
            return null;
        }
        return collect($this->queries)->count();
    }

    /**
     * Returns a collection of all db queries without their bindings
     *
     * @return Collection|null
     */
    public function getSanitizedDBQueries() {
        if($this->queries === null) {
            return null;
        }
        return collect($this->queries)->transform(function($query) {
            return [
                'query' => $query['query'],
                'time' => $query['time'],
                'bindingsIdentifier' => Hash::make(json_encode($query['bindings'])),
            ];
        });
    }

    public function toArray() {
        return [
            'url' => $this->url,
            'started_at' => $this->started_at,
            'stopped_at' => $this->stopped_at,
            'queries' => $this->queries,
            'max_memory' => $this->max_memory,
        ];
    }
}
