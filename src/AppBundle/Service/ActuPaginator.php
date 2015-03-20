<?php

	namespace AppBundle\Service;

	class ActuPaginator {

		protected $doctrinePaginator;
		protected $numPerPage;

		public function setDoctrinePaginator($doctrinePaginator){
			$this->doctrinePaginator = $doctrinePaginator;
		}

		public function __construct($numPerPage){
			$this->numPerPage = $numPerPage;
		}

		public function getPaginationData($page){
			$data = array();
			$data['totalActus']     = count($this->doctrinePaginator);
			$data['firstShowing']   = $this->numPerPage * ($page - 1) + 1;
			$data['lastShowing']    = $data['firstShowing'] + $this->numPerPage;
			if ($data['lastShowing'] > $data['totalActus']){
			    $data['lastShowing'] = $data['totalActus'];
			}
			$data['hasPrevPage']    = ($page > 1) ? true : false;
			$data['lastPage']       = ceil($data['totalActus'] / $this->numPerPage);
			$data['hasNextPage']    = ($page < $data['lastPage']) ? true : false;

			$data['numLinks']       = 2;
			$data['minNumLink']     = ($page-$data['numLinks'] < 1) ? 1 : $page-$data['numLinks'];
			$data['maxNumLink']     = ($page+$data['numLinks'] > $data['lastPage']) ? $data['lastPage'] : $page+$data['numLinks'];
		
			return $data;
		}

	}