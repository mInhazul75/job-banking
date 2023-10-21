<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;

use App\Helpers\Helper;


class TransactionController extends Controller
{
	public function allTransactions(Request $request)
	{
		try {
			$transactions = Transaction::with('currentBalanceInfo:id,name,balance')->get();
			return Helper::responseSuccess("All Transaction Return Succesfully", $transactions);
		} catch (\Exception $e) {
			return Helper::responseError($e->getMessage());
		}
	}

	public function allDeposit(Request $request)
	{
		try {
			$deposits = Transaction::where('transaction_type', 'deposit')->get();
			return Helper::responseSuccess("All Deposits Return Wise Succesfully", $deposits);
		} catch (\Exception $e) {
			return Helper::responseError($e->getMessage());
		}
	}

	public function allwithdrawal(Request $request)
	{
		try {
			$withdrawals = Transaction::where('transaction_type', 'withdrawal')->get();
			return Helper::responseSuccess("All Withdrawals Return Wise Succesfully", $withdrawals);
		} catch (\Exception $e) {
			return Helper::responseError($e->getMessage());
		}
	}


// post
	public function depositTrans(Request $request)
	{
		try {
			$user = auth('sanctum')->user();
			if (!$user) {
				return Helper::responseError('User not found');
			}

			$user_id = $user->id;
			$transaction_type = 'deposit';
			$amount = $request->amount;
			$fee = $request->fee;
			$date = date('Y-m-d');

			$newDeposit = new Transaction();
			$newDeposit->user_id = $user_id;
			$newDeposit->transaction_type = $transaction_type;
			$newDeposit->amount = $amount;
			$newDeposit->fee = $fee;
			$newDeposit->date = $date;
			$newDeposit->save();

			User::where('id', $user_id)->increment('balance', $amount);

			return Helper::responseSuccess("Depost Transaction Added  Succesfully");
		} catch (\Exception $e) {
			return Helper::responseError($e->getMessage());
		}
	}

	public function withdrawTrans(Request $request)
	{
		try {
			$user = auth('sanctum')->user();
			if (!$user) {
				return Helper::responseError('User not found');
			}

			$user_id = $user->id;
			$transaction_type = 'withdrawal';
			$amount = $request->amount;

			$calculatedFee = 0;

			if ($user->account_type == 'individual') {
				$currentDay = date('l'); // Get the current day
				$currentDate = date('Y-m-d'); // Get the current date
                // Check if it's a Friday and set the fee to 0
				if ($currentDay === 'Friday') {
					$calculatedFee = 0;
				} else {
            // Calculate the fee based on the amount
					if ($amount <= 1000) {
                // The first 1K withdrawal per transaction is free
						$calculatedFee = 0;
					} else {
                // Calculate the fee for amounts above 1K
						$calculatedFee = ($amount - 1000) / 100 * 0.015;
					}
				}

				$firstDayOfMonth = date('Y-m-01');
				if ($currentDate === $firstDayOfMonth && $amount <= 5000) {
					$calculatedFee = 0;
				}
			} else {
             // Check the user's total withdrawal amount
				$totalWithdrawal = Transaction::where('user_id', $user_id)
					->where('transaction_type', 'withdrawal')
					->sum('amount');

				if ($totalWithdrawal >= 50000) {
            // After a total withdrawal of 50K, decrease the fee to 0.015%
					$calculatedFee = $amount / 100 * 0.015;
				} else {
					$calculatedFee = $amount / 100 * 0.025;
				}
			}

			$fee = $calculatedFee;
			$date = date('Y-m-d');

			$newWithdraw = new Transaction();
			$newWithdraw->user_id = $user_id;
			$newWithdraw->transaction_type = $transaction_type;
			$newWithdraw->amount = $amount;
			$newWithdraw->fee = $calculatedFee; // Use the calculated fee
			$newWithdraw->date = $date;
			$newWithdraw->save();

			User::where('id', $user_id)->decrement('balance', $amount);

			return Helper::responseSuccess("Withdrawal Transaction Added  Succesfully");
		} catch (\Exception $e) {
			return Helper::responseError($e->getMessage());
		}
	}
}
