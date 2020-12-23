@extends('layouts.master')
@section('title')
    {{__('Quản lý học kì-Tuần')}}
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style>
        .p{
            position: absolute;
            top:0;
        }
        .error_time_semeser_create p{
            margin-left: 213px;
            color: red;
        }
        .error_time_semeser_edit p{
            margin-left: 213px;
            color: red;
        }
        .required{
            color: red;
        }

        .select2-selection__rendered {
            padding-top: 6px;
        }

        .errors {
            color: red;
            /* background-color: #acf; */
        }

    </style>
@endsection
@section('script')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{asset('assets/js/semester.js')}}"></script>
@endsection
@section('content')

    <div class="main-content">
        <section class="section">

            <div class="section-header">
                <h1>Học kỳ</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="{{route('backend.dashboard')}}">QLTT chung</a></div>
                  <div class="breadcrumb-item">Danh sách học kỳ</div>
                </div>
              </div>
            <div class="section-body">
                <h2 class="section-title">Danh sách học kỳ</h2>
                <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                            <a href="" class="btn btn-success btn-add"  title="Thêm mới học kỳ" style="border-radius: 5px!important;">Thêm mới</a>
                        </div>

                      <div class="row">
                          <div class="col-12">
                              <div class="card" style="width: 100%; border-radius: 5px; ">
                                  <div class="card-header" style=" background-color: #f5f5f5;min-height: 10px; height: 40px;">
                                      <h4 class="card-title" style="color: #6c757d; "><i class="fas fa-search"></i> Lọc học kỳ</h4>
                                  </div>
                                  <div class="card-body">
                                      <form class="form-inline" action="" method="post" id="formFilterFeedback">
                                          @csrf
                                          <div class="form-group col-4" >
                                              <select name="filterSemester" id="filterSemester" class="custom-select select2-selection__rendered">
                                                  <option value="">Tất cả các học kỳ</option>
                                                  <option value="1">1</option>
                                                  <option value="2">2</option>
                                                  <option value="3">3</option>
                                              </select>
                                          </div>
                                          <div class="form-group col-4">
                                              <select name="filterSchoolYear" id="filterSchoolYear" class="custom-select select2-selection__rendered">
                                                  <option value="">Tất cả năm học</option>
                                                  @foreach($school_year as $tg)
                                                      <option value="{{$tg}}">{{$tg}}</option>
                                                  @endforeach
                                              </select>
                                          </div>
                                          <div class="form-group col-4">
                                              <button class="btn btn-primary mb-2 filter"  style="margin-right: 5px"><i class="fas fa-search "></i> Lọc dữ liệu</button>
                                              <button class="btn btn-danger mb-2 reset" id="resetFilter"><i class="fas fa-undo-alt "></i> Đặt lại</button>
                                          </div>
                                      </form>
                                  </div>
                              </div>

                          <div class="card-body">
                            <table class="table table-striped" id="listSemeter">
                              <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Năm học</th>
                                    <th>Học kỳ</th>
                                    <th>Số Tuần</th>
                                    <th>Ngay bắt đầu học kỳ</th>
                                    <th>Ngày kết thúc học kỳ</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class=" modal-lg modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formAddSemester">
                    <div class="card-header">
                        <h4>Thêm mới</h4>
                    </div>
                    <div class="card-body ">
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Năm học<span >(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2 select2-input" name="add_school_year" id="add_school_year">
                                        <option value=""></option>
                                        <option value="{{ $yearNow_add ." - ". ++$yearNow_add }}">{{ --$yearNow_add ." - ". ++$yearNow_add }}</option>
                                        <option value="{{ $yearNow_add ." - ". ++$yearNow_add }}">{{ --$yearNow_add ." - ". ++$yearNow_add }}</option>
                                        <option value="{{ $yearNow_add ." - ". ++$yearNow_add }}">{{ --$yearNow_add ." - ". ++$yearNow_add }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Học kỳ<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2 select2-input" id="add_semester" name="add_semester" >
                                        <option value=""></option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số tuần<span class="required" >(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" id="add_number_weeks" name="add_number_weeks" value="" />
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4 " id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Ngày bắt đầu<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7" id="">
                                    <input class="form-control selectric" id="add_semester_start_date" name="add_semester_start_date" placeholder="dd/mm/yyyy"/>
                                </div>
                                <div class="error_time_semeser_create">
                                    <p></p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="" class="btn btn-primary " id="btnSaveformAddSemester" for="formAddSemester" >Thêm</button>
                                <button  class="btn btn-secondary" data-dismiss="modal">hủy</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class=" modal-lg modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formEditSemester">
                    <div class="card-header">
                        <h4>Sửa thông tin</h4>
                    </div>
                    <div class="card-body ">
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Năm học<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7" id="edit_school_year1">
                                    <select class="form-control selectric" name="edit_school_year" id="edit_school_year" disabled>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Học kỳ<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric from-Edit-select2-input" id="edit_semester" name="edit_semester" >
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số tuần<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" id="edit_number_weeks" name="edit_number_weeks" value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-group row mb-4 " id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Ngày bắt đầu<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7" id="">
                                    <input class="form-control selectric" id="edit_semester_start_date" name="edit_semester_start_date" placeholder="dd/mm/yyyy"/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="" class="btn btn-primary btn-save " id="btnSaveformEditSemester" for="formEditSemester" >Sửa</button>
                                <button  class="btn btn-secondary" data-dismiss="modal">hủy</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection


