@extends('profile.profile-layout')
@section('profile')
<div class="card">
    <div class="card-header">
        Update Profile
    </div>
    <div class="card-body">
        <form action="{{route('update_profile')}}" method="post" id="edit_profile_form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" name="first_name" value={{ (old('first_name'))?old('first_name'):$user->first_name }} class="form-control" id="first_name" placeholder="Enter first name">
                @if($errors->any('first_name'))
                    <span class="text-danger">{{ $errors->first('frst_name')}}</span>
                @endif
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" name="last_name"  value={{ (old('last_name'))?old('last_name'):$user->last_name }} class="form-control" id="last_name" placeholder="Enter last name">
                @if($errors->any('last_name'))
                <span class="text-danger">{{ $errors->first('last_name')}}</span>
            @endif
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection