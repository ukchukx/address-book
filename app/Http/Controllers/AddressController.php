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

    Log::info('Fetch contact addresses', ['user' => $user->email, 'contact' => $id]);

    if (! $contact) {
      Log::info('Cannot find contact', ['user' => $user->email, 'contact' => $id]);

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

    Log::info('Create address', ['params' => $requestData, 'user' => $user->email]);

    if (! $command->isValid()) {
      Log::info('Create address command is invalid', ['params' => $requestData, 'user' => $user->email]);

      return response([
        'success' => false,
        'message' => 'Invalid params'
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $address = $command->execute();

    if ($address) {
      Log::info('Address created', ['params' => $requestData, 'user' => $user->email, 'address' => $address->id]);

      return response([
        'success' => true,
        'message' => 'Created',
        'data' => $address
      ], Response::HTTP_CREATED);
    }


    return response(['success' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function update(Request $request, $id) {
    $user = Auth::guard('api')->user();
    $address = Address::find($id);
    $data = array_merge(['address_id' => $id], $request->only(['key', 'value']));

    Log::info('Update address', ['user' => $user->email, 'params' => $data]);

    if ($address->contact->user_id == $user->id) {
      $address = UpdateAddress::from($data)->execute();

      Log::info($address ? 'Address updated' : 'Could not update address', ['user' => $user->email, 'params' => $data]);

      return response([
        'success' => (bool) $address,
        'data' => $address,
        'message' => ((bool) $address) ? 'Updated' : 'Not updated'
      ], ((bool) $address) ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    Log::info('Cannot update address', ['user' => $user->email, 'params' => $data]);

    return response(null, Response::HTTP_FORBIDDEN);
  }

  public function destroy($id) {
    $user = Auth::guard('api')->user();
    $address = Address::find($id);

    if (! $address) return response(null, Response::HTTP_NOT_FOUND);

    Log::info('Delete address', ['user' => $user->email, 'address' => $id]);

    if ($address->contact->user_id == $user->id) {
      DeleteAddress::from(['id' => $address->id])->execute();

      Log::info('Address deleted', ['user' => $user->email, 'address' => $id]);

      return response(null, Response::HTTP_NO_CONTENT);
    }

    Log::info('Cannot delete address', ['user' => $user->email, 'address' => $id]);

    return response(null, Response::HTTP_FORBIDDEN);
  }
}
