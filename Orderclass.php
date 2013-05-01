<?php
require_once 'c:/inetpub/wwwroot/cgi-bin/IncludeCode/Classes/orderitemsclass.php';
class Order{
	public $customercomment,$status,$ordernum,$shipmethod,$note,$customername,$emailaddress,$printCard,$eCard,$wineclub,$date;
	private $dbconnection;


	public function Order($conn="",$ordernum=0){
		$this->dbconnection=$conn;
		if($ordernum)
			$this->load($ordernum);
	}
	
	public function load($ordernum){
		$query = mssql_init("webdata_HOMSOrderGet");
		$param1 = $ordernum;	
		mssql_bind($query, "@ordernum", $param1, SQLVARCHAR);
		$result = mssql_execute($query);
		$row = mssql_fetch_array($result);
	
		 $this->ordernum=$row['ordernum'];
		 $this->customercomment=$row['comments'];
		 $this->status=$row['status'];
		 $this->shipmethod=$row['shippingtype'];
		 $this->note=$row['note'];
		 $this->customername=$row['name'];
		 $this->emailaddress=$row['emailaddress'];
		 $this->printCard=$row['printCard'];
		 $this->eCard=$row['eCard'];
		 $this->wineclub=$row['wineclub'];
		 $this->date=$row['date'];
		//Only the first months's shipment do we send the note card, if it is greater then the first month then set the notecard to blank.
		 if($this->wineclub=='y' && $this->GetIssueNumber()>1)
			$this->note='';
	}
	//this function should be called "Load" but this was added after class was initially created.
	private function load_rec($row){
		if($row===false){
			return false;
		}
		 $this->ordernum=$row['ordernum'];
		 $this->customercomment=$row['comments'];
		 $this->status=$row['status'];
		 $this->shipmethod=$row['shippingtype'];
		 $this->note=$row['note'];
		 $this->customername=$row['name'];
		 $this->emailaddress=$row['email'];
		 $this->printCard=$row['printCard'];
		 $this->eCard=$row['eCard'];
		 $this->wineclub=$row['wineclub'];
		 $this->date=$row['date'];
		//Only the first months's shipment do we send the note card, if it is greater then the first month then set the notecard to blank.
		 if($this->wineclub=='y' && $this->get_issue_number()>1)
			$this->note='';
			return 1;
		}
		
		
	//load all order information from a specific cart that contains orders. No wineclubs will be in these carts.
	public static function load_by_cart($cartnum){
			$sql="select b.ordernum,s.comments,b.status,s.shippingtype,s.note,b.name,b.email,s.printCard,s.eCard,'n' as wineclub,b.date
					FROM billing b JOIN shipping s on b.ordernum=s.ordernum
					where b.ordernum in (select ordernum from fulfillment_orders where cartnum={$cartnum})";
			$result = mssql_query($sql); 
			$result_arr=array();
			while($row=mssql_fetch_array($result,MSSQL_ASSOC)){  			
				$obj=new Order();
				$obj->load_rec($row);
				if($obj!==false) $result_arr[$obj->ordernum]=$obj;
			}  
			return $result_arr;   
			
	}
	
	//This function is used for Wineclubs. It extracts the issue number from the shipment id. It is used to determine if a note card should be shown.
	private function get_issue_number(){
		$expString=explode('-',$this->ordernum);
		return $expString[1];
	}
	public static function get_note_card_file_name($dbconnection){
		$query = mssql_init("webdata_notecardnumber", $dbconnection);
		$result = mssql_execute($query);
		$row = mssql_fetch_array($result);
		return($row[0].'.txt');
	}
?>