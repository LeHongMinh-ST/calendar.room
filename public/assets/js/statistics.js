class Statistics{

    constructor() {
        this.even();
    }

    even() {
        let thisStatistics = this, semester_id = $('#filterSemester').val();
        $('#formFilter').on('click','.btn-primary',function (e) {
            let room_id = $('#filterRoom').val(), faculty_id = $('#filterFaculty').val();
            let yearSchool = $('#filterYear').val(), semester_id = $('#filterSemester').val();
            e.preventDefault();
            if(!thisStatistics.valiFilterSession()) {
                thisStatistics.StatisticsSession();
                return;
            }
            thisStatistics.StatisticsSession(yearSchool, semester_id, room_id, faculty_id);
        });

        $('#formFilterSG').on('click','.btn-primary',function (e) {
            e.preventDefault();
            let filterYearGroup = $('#filterYearSG').val(), semester_id = $('#filterSemesterSG').val();
            let room_id = $('#filterRoomSG').val(), faculty_id = $('#filterFacultySG').val();
            if(!thisStatistics.valiFilterGroup()) {
                thisStatistics.StatisticsGroup();
                return;
            }
            thisStatistics.StatisticsGroup(filterYearGroup,semester_id,room_id,faculty_id);
        });

        //Sự kiện reset lại bộ lọc của bảng
        $("#resetFilter").click(function(e){
            e.preventDefault();
            $("#filterSemester").select2().val(null).trigger('change');
            $("#filterRoom").select2().val(null).trigger('change');
            $("#filterFaculty").select2().val(null).trigger('change');
            $("#filterYear").select2().val(null).trigger('change');
            thisStatistics.StatisticsSession();
        });

        $("#resetFilterSG").click(function(e){
            e.preventDefault();
            $("#filterSemesterSG").select2().val(null).trigger('change');
            $("#filterRoomSG").select2().val(null).trigger('change');
            $("#filterFacultySG").select2().val(null).trigger('change');
            $("#filterYearSG").select2().val(null).trigger('change');
            thisStatistics.StatisticsGroup();
        });

        $("#filterYearSG").change(function (){
            let schoolYear = $('#filterYearSG').val();
            thisStatistics.getSemester("filterSemesterSG", schoolYear);
        });

        $("#filterYear").change(function (){
            let schoolYear = $('#filterYear').val();
            thisStatistics.getSemester("filterSemester", schoolYear);
        });
    }

    StatisticsSession(yearSchool, semester_id, room_id, faculty_id){
        let table = $("#statisticsSession").DataTable({
            language: {
                sProcessing:   "Đang xử lý...", sLengthMenu:   "Xem _MENU_ mục",
                sZeroRecords: "Không tìm thấy dữ liệu nào phù hợp", sEmptyTable: "",
                sInfo:         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục", sInfoEmpty:    "Đang xem 0 đến 0 trong tổng số 0 mục",
                sInfoFiltered: "(được lọc từ _MAX_ mục)", sSearch: 'Tìm kiếm', lengthMenu: '_MENU_ bản ghi/trang',
                oPaginate: { "sFirst":    "Đầu", "sPrevious": "Trước", "sNext":     "Tiếp", "sLast":     "Cuối" }
            }, processing: true, serverSide: true, searching: false, destroy: true, responsive: true, paging: false, info: false,
            ajax: {
                type: 'get',
                url: '/admin/statistics/getData-number-session',
                data: {'year_School': yearSchool, 'semester': semester_id, 'room_id': room_id, 'faculty_id': faculty_id },
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false},
                {data: 'subject_id', name: 'subject_id'},
                {data: 'subject_group', name: 'subject_group'},
                {data: 'class', name: 'class'},
                {data: 'amount_people', name: 'amount_people'},
                {data: 'number_session', name: 'number_session'},
                {data: 'sum_session', name: 'sum_session'},
            ],
            "drawCallback": function(){
                var api = this.api();
                if (!yearSchool && !semester_id && !room_id && !faculty_id){
                    $('.dataTables_empty').remove();
                    $(api.column(6).footer()).html("");
                }else {
                    $(api.column(6).footer()).html(
                        api.column(6, {
                            page: 'current'
                        }).data().sum()
                    )
                }
                $(api.column(0).footer()).html('Tổng số tiết');
            }
        });
    }

    StatisticsGroup(yearSchool, semester_id, room_id, faculty_id){
        let table = $("#statisticsGroup").DataTable({
            language: {
                sProcessing:   "Đang xử lý...", sLengthMenu:   "Xem _MENU_ mục",
                sZeroRecords: "Không tìm thấy dữ liệu nào phù hợp", sEmptyTable: "",
                sInfo:         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục", sInfoEmpty:    "Đang xem 0 đến 0 trong tổng số 0 mục",
                sInfoFiltered: "(được lọc từ _MAX_ mục)", sSearch: 'Tìm kiếm', lengthMenu: '_MENU_ bản ghi/trang',
                oPaginate: { "sFirst":    "Đầu", "sPrevious": "Trước", "sNext":     "Tiếp", "sLast":     "Cuối" }
            }, processing: true, serverSide: true, searching: false, destroy: true, responsive: true, paging: false, info: false,
            ajax: {
                type: 'get',
                url: '/admin/statistics/getData-Subject-Group',
                data: {'year_School': yearSchool, 'semester': semester_id, 'room_id': room_id, 'faculty_id': faculty_id },
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false},
                {data: 'room_id', name: 'room_id'},
                {data: 'subject_group', name: 'subject_group'},
                {data: 'class', name: 'class'},
                {data: 'amount_people', name: 'amount_people'},
            ],
            "drawCallback": function(){
                var api = this.api();
                if (!yearSchool && !semester_id && !room_id && !faculty_id){
                    $('.dataTables_empty').remove();
                    $(api.column(4).footer()).html("");
                }else {
                    $(api.column(4).footer()).html(
                        api.column(2, {
                            page: 'current'
                        }).data().count()
                    )
                }
                $(api.column(0).footer()).html('Tổng số nhóm');
            }
        });
    }

    getSemester(idFilter, schoolYear){
        $('#'+idFilter).empty();
        $.ajax
        ({
            type: 'post',
            url: '/admin/statistics/get-data-Semester',
            data: {'year_School': schoolYear},
            success: function (res) {
                let arr = res.listSemester;
                let option = new Option('Tất cả học kì', '', true, true);
                $("#"+idFilter).append(option).trigger('change');
                for(let i=0; i<arr.length; i++){
                    $('#'+idFilter).append('<option value="'+arr[i].id+'">'+arr[i].semester+'</option>')
                }
            }
        });
    }

    valiFilterSession(){
        $('#formFilter').validate({
        errorClass: 'errors',
        rules:{
            filterYear:{
                required:true,
            },
        },
        messages:{
            filterYear:{
                required:"vui lòng chọn năm học!",
            },
        },
            errorPlacement: function(error, element){
                let id = element.attr('id');
                if(element.hasClass('select2-input')){
                    error.insertAfter($('#select2-'+id+'-container').parent(element));
                }else if(element.hasClass('datepicker')){
                    error.insertAfter($('#'+id).parent(element));
                }else{
                    error.insertAfter(element);
                }
            }
        });
         return $('#formFilter').valid()
    }

    valiFilterGroup(){
        $('#formFilterSG').validate({
            errorClass: 'errors',
            rules:{
                filterYearSG:{
                    required:true,
                },
            },
            messages:{
                filterYearSG:{
                    required:"vui lòng chọn năm học!",
                },
            },
            errorPlacement: function(error, element){
                let id = element.attr('id');
                if(element.hasClass('select2-input')){
                    error.insertAfter($('#select2-'+id+'-container').parent(element));
                }else if(element.hasClass('datepicker')){
                    error.insertAfter($('#'+id).parent(element));
                }else{
                    error.insertAfter(element);
                }
            }
        });
        return $('#formFilterSG').valid()
    }
}

$(document).ready(function (){
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $("#filterSemester").select2(); $('#filterRoom').select2(); $('#filterFaculty').select2();
    $('#filterYear').select2();
    $("#filterSemesterSG").select2(); $('#filterRoomSG').select2(); $('#filterFacultySG').select2();
    $('#filterYearSG').select2();


    let statistics = new Statistics();

});
