<?php

return [
    /**
     * API Token Key (string)
     * Accepted value:
     * Live Token: https://myfatoorah.readme.io/docs/live-token
     * Test Token: https://myfatoorah.readme.io/docs/test-token
     */
    'api_key' => 'VulL8ESk9BjC571DYk1lM5u1zNlxz6XtNdDUl70sOcGJ9qbbUpDsSymThH4BW9FuJec06LaF1eypVo-Y6wy_-CauqNWTfZ9IYub9NhsSh8plNyOxQnhGvrKExHyEPdTOL_qUhPVbKCNCac3tW-L0eEOUVd8hzjoBVVnzxNtjYiqRzoHoSkdL5OsKMsKJEjcF4NOwwOE8yKGEiN8-CPVVcGPv-wR4x9HSr-EgadWi5bR9uvqIRmhjwfekaPII0zARlQ3-Yn7JeVd_tE2zTkEhJCTCiYyLWR8XsHEI8SIsl6gCIO2R9sMYA8XHv6TMZDxvGC5qxEfpuoW-SK2_ooJW5eXqNs37Z9rvParwaUwc4MuIKRh5P7sk8C0KRYJwidPpFZ0tnw53B5xZ0HU11yiSOu4Leq6ZmP7mnwkuiX-1RiZsipdbs6VdiPJnsEf_hxr_iw1vPRoBSk0RcPCoB03GMebsDmmXKo4V33NqJTk82zvWN0xYcHYKUskGg_aAyXX4iFRiAagxNPMNbk165XCDu7riw4yR4FoEzmvImvf87e7bF3waeIxJjLFvIlgXkS6f1ibYFH7Bn-dqHVU40Tz_I6fFrgaGo_oUAQSQu71F1jgPiuBKy96gSKsNbpHaWwuptw0lDyy8uYfgv2l8816WJJaz1iMnvBSpEhu8UQGDBAmOj3iVu7KSwHP3YI5nnUgY_7zKwQ',
    /**
     * Test Mode (boolean)
     * Accepted value: true for the test mode or false for the live mode
     */
    'test_mode' => false,
    /**
     * Country ISO Code (string)
     * Accepted value: KWT, SAU, ARE, QAT, BHR, OMN, JOD, or EGY.
     */
    'country_iso' => 'SAU',
    /**
     * Save card (boolean)
     * Accepted value: true if you want to enable save card options.
     * You should contact your account manager to enable this feature in your MyFatoorah account as well.
     */
    'save_card' => true,
    /**
     * Webhook secret key (string)
     * Enable webhook on your MyFatoorah account setting then paste the secret key here.
     * The webhook link is: https://{example.com}/myfatoorah/webhook
     */
    'webhook_secret_key' => '',
    /**
     * Register Apple Pay (boolean)
     * Set it to true to show the Apple Pay on the checkout page.
     * First, verify your domain with Apple Pay before you set it to true.
     * You can either follow the steps here: https://docs.myfatoorah.com/docs/apple-pay#verify-your-domain-with-apple-pay or contact the MyFatoorah support team (tech@myfatoorah.com).
     */
    'register_apple_pay' => false
];
