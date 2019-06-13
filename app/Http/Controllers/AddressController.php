<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use App\Models\Address;
use App\Domain\Address\Commands\CreateAddress;
use App\Domain\Address\Commands\UpdateAddress;
use App\Domain\Address\Commands\DeleteAddress;

class AddressController extends Controller {
  public function index($id) {
    $contact = Auth::guard('api')->user()->findContact($id);

    if (! $contact) return response(null, Response::HTTP_FORBIDDEN);

    return response([
      'success' => true,
      'data' => $contact->addresses()
    ], Response::HTTP_OK);
  }

  public function store(Request $request) {
    $command = CreateAddress::from($request->all());

    if (! $command->isValid()) {
      return response([
        'success' => false,
        'message' => 'Invalid params'
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $address = $command->execute();

    return response([
      'success' => true,
      'message' => 'Created',
      'data' => $address
    ], Response::HTTP_CREATED);
  }

  public function update(Request $request, $id) {
    $user = Auth::guard('api')->user();
    $address = Address::find($id);

    if ($address->contact()->userId == $user->id) {
      $address = UpdateAddress::from(array_merge(['address_id' => $id], $request->only(['key', 'value'])))->execute();

      return response([
        'success' => (bool) $address,
        'data' => $address,
        'message' => ((bool) $address) ? 'Updated' : 'Not updated'
      ], ((bool) $address) ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    return response(null, Response::HTTP_FORBIDDEN);
  }

  public function destroy($id) {
    $user = Auth::guard('api')->user();
    $address = Address::find($id);

    if ($address->contact()->userId == $user->id) {
      DeleteAddress::from(['id' => $address->id])->execute();

      return response(null, Response::HTTP_NO_CONTENT);
    }

    return response(null, Response::HTTP_FORBIDDEN);
  }
}
