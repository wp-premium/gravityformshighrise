<?php
namespace Highrise;

	class HighriseKase extends HighriseAPI
	{
		private $highrise;
		public $id;

		public $account_id;
		public $author_id;
		public $category_id;
		public $created_at;
		public $updated_at;
		public $group_id;
		public $visible_to;
		public $name;
		public $owner_id;
		public $party_id;
		public $parties;
		public $party;
		
		public function __construct(HighriseAPI $highrise)
		{
			$this->highrise = $highrise;
			$this->account = $highrise->account;
			$this->token = $highrise->token;
			$this->debug = $highrise->debug;
			$this->curl = curl_init();		
			$this->parties = array();

		}

		public function setId($deal_id)
		{
			$this->id = (string)$deal_id;
		}

		public function getId()
		{
			return $this->id;
		}

		public function setOwnerId($owner_id)
		{
			$this->owner_id = (string)$owner_id;
		}

		public function getOwnerId()
		{
			return $this->owner_id;
		}

		
		public function setAccountId($account_id)
		{
			$this->account_id = (string)$account_id;
		}

		public function getAccountId()
		{
			return $this->account_id;
		}

		public function setAuthorId($author_id)
		{
			$this->author_id = (string)$author_id;
		}

		public function getAuthorId()
		{
			return $this->author_id;
		}

		
		
		public function setBackground($background)
		{
			$this->background = (string)$background;
		}

		public function getBackground()
		{
			return $this->background;
		}

		
		public function setCategoryId($category_id)
		{
			$this->category_id = (string)$category_id;
		}

		public function getCategoryId()
		{
			return $this->category_id;
		}

		
		// this shouldn't really be a function...
		public function setClosedAt($created_at)
		{
			$this->closed_at = (string)$created_at;
		}

		public function getClosedAt()
		{
			return $this->closed_at;
		}

		public function setCreatedAt($created_at)
		{
			$this->created_at = (string)$created_at;
		}

		public function getCreatedAt()
		{
			return $this->created_at;
		}

		
		public function setCurrency($currency = 'USD')
		{
			$this->currency = (string)$currency;
		}

		public function getCurrency()
		{
			return $this->currency;
		}

		public function setDuration($duration)
		{
			$this->duration = (string)$duration;
		}

		public function getDuration()
		{
			return $this->duration;
		}

		
		public function setGroupId($group_id)
		{
			$this->group_id = (string)$group_id;
		}

		public function getGroupId()
		{
			return $this->group_id;
		}

		
		public function setName($name)
		{
			$this->name = (string)$name;
		}

		public function getName()
		{
			return $this->name;
		}

		public function setPartyId($party_id)
		{
			$this->party_id = (string)$party_id;
		}

		public function getPartyId()
		{
			return $this->party_id;
		}

		
		public function setPrice($price)
		{
			$this->price = (string)$price;
		}

		public function getPrice()
		{
			return $this->price;
		}

		public function setResponsiblePartyId($responsible_party_id)
		{
			$this->responsible_party_id = (string)$responsible_party_id;
		}

		public function getResponsiblePartyId()
		{
			return $this->responsible_party_id;
		}

		public function setStatus($status)
		{
			$valid_statuses = array("pending", "won", "lost");
			$status = strtolower($status);
			if ($status != null && !in_array($status, $valid_statuses)) {
				throw new \Exception("$status is not a valid status. Available statuses: " . implode(", ", $valid_statuses));
			}
			$this->status = (string)$status;

		}

		public function getStatus()
		{
			return $this->status;
		}

		// TODO:  shouldn't be a function.
		public function setStatusChangedOn($status_changed_on)
		{
			$this->status_changed_on = (string)$status_changed_on;
		}

		public function getStatusChangedOn()
		{
			return $this->status_changed_on;
		}

		public function setUpdatedAt($updated)
		{
			return $this->updated_at = (string) $updated;
		}

		public function getUpdatedAt()
		{
			return $this->updated_at;
		}

		public function getVisibleTo()
		{
			return $this->visible_to;
		}

		public function setVisibleTo($updated)
		{
			return $this->visible_to = (string) $updated;
		}

		// no set parties or party since they're "special"
		public function getParties()
		{
			return $this->parties;
		}

		public function getParty()
		{
			return $this->party;
		}

		public function loadFromXMLObject($xml_obj)
		{
	
			if ($this->debug)
				print_r($xml_obj);

			$this->setId($xml_obj->{'id'});
			$this->setAuthorId($xml_obj->{'author-id'});
			$this->setCreatedAt($xml_obj->{'created-at'});
			$this->setClosedAt($xml_obj->{'closed-at'});
			$this->setGroupId($xml_obj->{'group-id'});
			$this->setName($xml_obj->{'name'});
			$this->setOwnerId($xml_obj->{'owner-id'});
			$this->setUpdatedAt($xml_obj->{'updated-at'});
			$this->setVisibleTo($xml_obj->{'visible-to'});
			$this->loadPartyFromXMLObject($xml_obj->{'party'});
			$this->loadPartiesFromXMLObject($xml_obj->{'parties'});

			return true;
		}


		function loadPartyFromXMLObject($xml_obj) {

			if ($xml_obj != null) {
				$this->party = new HighriseParty($this->highrise);
				$this->party->loadfromXMLObject($xml_obj);
			}

		}

		function loadPartiesFromXMLObject($xml_obj) {
			foreach ($xml_obj->{'party'} as $party_obj) {
				$new_party = new HighriseParty($this->highrise);
				$new_party->loadFromXMLObject($party_obj);
				$this->parties[] = $new_party;
			} 
		}

	}
	
