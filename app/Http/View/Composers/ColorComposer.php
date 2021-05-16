<?php

namespace App\Http\View\Composers;

use App\Settings;
use Illuminate\View\View;

class ColorComposer
{
	/**
	 * The setting resource implementation.
	 *
	 * @var \App\Settings
	 */
	protected $setting;

	/**
	 * Create a new profile composer.
	 *
	 * @param  \App\Settings  $settings
	 * @return void
	 */
	public function __construct(Settings $settings)
	{
		// Dependencies are automatically resolved by the service container...
		$this->setting = $settings->first();
	}

	/**
	 * Bind data to the view.
	 *
	 * @param  \Illuminate\View\View  $view
	 * @return void
	 */
	public function compose(View $view)
	{
		$view->with('colors', $this->setting);
	}
}
