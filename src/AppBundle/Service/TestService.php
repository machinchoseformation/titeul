<?php

	namespace AppBundle\Service;

	class TestService {

		protected $doctrine;

		public function __construct($doctrine, $string, $database_name){
			echo $string;
			echo $database_name;
			$this->doctrine = $doctrine;
		}

		public function yo(){
			dump($this->doctrine->getManager());
			die("yo!!");
		}

	}



