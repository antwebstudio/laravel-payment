<?php

Route::get('/invoice/{invoice}/payment/bank-wire/', 'PaymentController@bankWire')->name('invoice.payment.bank_wire');