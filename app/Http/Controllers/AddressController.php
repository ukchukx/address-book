<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use Log;
use App\Models\Address;
use App\Domain\Address\Commands\CreateAddress;
use App\Domain\Address\Commands\UpdateAddress;
use App\Domain\Address\Commands\DeleteAddress;

class AddressController extends Controller {
  public function index($id) {
    $user = Auth::guard('api')->user();
    $contact = $user->findContact($id);
    $logParams = ['user' => $user->email, 'contact' => $id];

    Log::info('Fetch contact addresses', $logParams);

    if (! $contact) {
      Log::warn('Cannot find contact', $logParams);

      return response(null, Response::HTTP_FORBIDDEN);
    }

    return response([
      'success' => true,
      'data' => $contact->addresses()->get()
    ], Response::HTTP_OK);
  }

  public function store(Request $request) {
    $user = Auth::guard('api')->user();
    $requestData = $request->all();
    $command = CreateAddress::from($requestData);
    $logParams = ['user' => $user->email, 'params' => $requestData];

    if (! $command->isValid()) {
      Log::warn('Create address command is invalid', $logParams);

      return response([
        'success' => false,
        'message' => 'Invalid params'
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $address = $command->execute();

    if ($address) {
      Log::info('Address created', array_merge($logParams, ['address' => $address->id]));

      return response([
        'success' => true,
        'message' => 'Created',
        'data' => $address
      ], Response::HTTP_CREATED);
    } else {
      Log::error('Could not create address', $logParams);
    }

    return response(['success' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function update(Request $request, $id) {
    $user = Auth::guard('api')->user();
    $address = Address::find($id);
    $data = array_merge(['address_id' => $id], $request->only(['key', 'value']));
    $logParams = ['user' => $user->email, 'params' => $data];

    if ($address->contact->user_id == $user->id) {
      $address = UpdateAddress::from($data)->execute();

      Log::info($address ? 'Address updated' : 'Could not update address', $logParams);

      return response([
        'success' => (bool) $address,
        'data' => $address,
        'message' => ((bool) $address) ? 'Updated' : 'Not updated'
      ], ((bool) $address) ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    Log::error('Cannot update address', $logParams);

    return response(null, Response::HTTP_FORBIDDEN);
  }

  public function destroy($id) {
    $user = Auth::guard('api')->user();
    $address = Address::find($id);
    $logParams = ['user' => $user->email, 'address' => $id];

    if (! $address) {
      Log::warn('Address not found', $logParams);

      return response(null, Response::HTTP_NOT_FOUND);
    }

    if ($address->contact->user_id == $user->id) {
      DeleteAddress::from(['id' => $address->id])->execute();

      Log::info('Address deleted', $logParams);

      return response(null, Response::HTTP_NO_CONTENT);
    }

    Log::error('Cannot delete address', $logParams);

    return response(null, Response::HTTP_FORBIDDEN);
  }
}
