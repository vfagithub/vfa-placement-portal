<?php

use Illuminate\Database\Migrations\Migration;

class AddEmailOptOutToHiringManagers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Schema::make
		
		Schema::table('hiringManagers', function($t) {
			$t->boolean('emailOptOut')->default(0);
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		
		Schema::table('hiringManagers', function($t) {
			$t->dropColumn('emailOptOut');
		});
	}

}