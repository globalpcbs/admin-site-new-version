<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order_tb extends Model
{
    //
    protected $table = 'order_tb';
    protected $primaryKey = 'ord_id';
    public $timestamps = false;
    protected $fillable = [
        'cust_name', 'part_no', 'rev', 'req_by', 'email', 'phone', 'fax',
        'qnt', 'lead_times', 'quote_by', 'necharge', 'descharge',
        'special_inst', 'special_instadmin', 'is_spinsadmact', 'ipc_class',
        'no_layer', 'm_require', 'thickness', 'thickness_tole', 'inner_copper',
        'start_cu', 'plated_cu', 'fingers_gold', 'trace_min', 'space_min',
        'con_impe_sing', 'con_impe_diff', 'tore_impe', 'hole_size', 'pad',
        'desdesc', 'desdesc1', 'desdesc2','descharge','descharge1','descharge2',
        'blind', 'buried', 'hdi_design', 'finish', 'mask_size', 'mask_type',
        'color', 'ss_side', 'ss_color', 'route', 'board_size1', 'board_size2',
        'array', 'b_per_array', 'array_size1', 'array_size2', 'route_tole',
        'array_design', 'design_array', 'array_type1', 'array_type2',
        'array_require1', 'array_require2', 'array_require3', 'bevel',
        'counter_sink', 'cut_outs', 'slots', 'logo', 'mark', 'date_code',
        'other_marking', 'micro_section', 'test_stamp', 'in_board', 'array_rail',
        'xouts', 'xouts1', 'rosh_cert', 'ord_date', 'cancharge', 'ccharge',
        'fob', 'orddate1', 'price', 'price1', 'price2', 'price3', 'price4',
        'qty1', 'qty2', 'qty3', 'qty4', 'day1', 'day2', 'day3', 'day4',
        'con_impe1', 'day5', 'qty5', 'qty6', 'qty7', 'qty8', 'qty9', 'qty10',
        'new_or_rep', 'pr11', 'pr12', 'pr13', 'pr14', 'pr15', 'pr21', 'pr22',
        'pr23', 'pr24', 'pr25', 'pr31', 'pr32', 'pr33', 'pr34', 'pr35', 'pr41',
        'pr42', 'pr43', 'pr44', 'pr45', 'pr51', 'pr52', 'pr53', 'pr54', 'pr55',
        'pr61', 'pr62', 'pr63', 'pr64', 'pr65', 'pr71', 'pr72', 'pr73', 'pr74',
        'pr75', 'pr81', 'pr82', 'pr83', 'pr84', 'pr85', 'pr91', 'pr92', 'pr93',
        'pr94', 'pr95', 'pr101', 'pr102', 'pr103', 'pr104', 'pr105',
        'descharge1', 'descharge2', 'cond_vias', 'resin_filled', 'sp_reqs',
        'comments', 'simplequote', 'fob_oth', 'vid', 'vid_oth',
    ];

}