<?php
  
	function test()
	{
		echo func_num_args();
	}
	
	test();
	test(1, 2);
	$whatever = NULL;
	test($whatever);


?>