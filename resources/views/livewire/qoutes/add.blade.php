<div>
    <div>
        @include('includes.flash')
        <form class="border-2" wire:submit.prevent="save">
            <input type="hidden" name="specialreqval" wire:model="sp_reqs">

            <table width="100%" class="border-primary" border="1" cellpadding="1" cellspacing="1" bordercolor="#e6e6e6"
                class="up" style="left:35px;">
                <tr>
                    <td height="25" bgcolor='#336699' colspan="3" align="center">
                        <font color="white"><span class="style1">Online Request For Quote Form</font></span>
                    </td>
                </tr>

                <tr>
                    <td height="25" class="p-2" colspan='3'><strong>Lookup ID :</strong>
                        <input type="text" size="60" wire:model="search" wire:keyup="onKeyUp($event.target.value)"
                            autocomplete="off" />
                        @if($matches)
                        <ul class="list-group position-absolute w-100 shadow-sm"
                            style="z-index:1050; max-height:220px; overflow-y:auto;">
                            @foreach($matches as $i => $m)
                            <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                                wire:click="useMatch({{ $i }})">
                                {{ $m['label'] }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td width="312" class="p-2" height="25"><strong>Customer : </strong>
                        <!-- <input type="text" wire:model="cust_name" wire:key="cust_name-{{ $inputKey }}" /> -->
                        <select wire:model="cust_name" id="cust_name" wire:change="changecustomer" wire:key="cust_name-{{ $inputKey }}">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->c_name }}" @selected($cust_name==$customer->c_name)>
                                {{ $customer->c_name }}
                            </option>
                            @endforeach
                        </select> <br />
                        @error('cust_name')
                        <font color="red"><small>{{ $message }}</small></font>
                        @enderror
                    </td>

                    <td width="252"><strong>Part Number :</strong>
                        <input type="text" name="txtpno" wire:model="part_no" wire:key="part-{{ $inputKey }}" /> <br />
                        @error('part_no')
                        <font color="red"><small>{{ $message }}</small></font>
                        @enderror
                    </td>

                    <td width="238"><strong>Rev :</strong>
                        <input name="txtrev" wire:model="rev" wire:key="rev-{{ $inputKey }}" size="2" />
                        <label for="new"><strong> New</strong></label>
                        <input type="radio" name="nor1" wire:model.live="new_or_rep" value="New Part" id="new" />
                        &nbsp;&nbsp;&nbsp;
                        <label for="rep"><strong>Repeat</strong></label>
                        <input type="radio" name="nor1" wire:model.live="new_or_rep" value="Repeat Order" id="rep" />
                    </td>
                </tr>

                <tr>
                    <td height="25" class="p-2"><strong>Requested By :</strong>
                        <select class="w-50" wire:change="requestby" wire:model="request_by">
                            <option>Select Requested By</option>
                             @foreach($customers_main as $main)
                                <option value="{{ $main->name }}" @if($request_by == $main->name) selected @endif>
                                    {{ $main->name }}
                                </option>
                            @endforeach
                            @foreach($customers_eng as $eng)
                                <option value="{{ $eng->name }}" @if($request_by == $eng->name) selected @endif>
                                    {{ $eng->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td colspan="2" class="p-2" id="content2" height="25">
                        <strong>Email : </strong>
                        <input type="text" name="txtemail" wire:key="email-{{ $inputKey }}" wire:model="email" />
                        <strong>Phone :</strong>
                        <input type="text" name="txtphone" wire:key="phone-{{ $inputKey }}" wire:model="phone" />
                    </td>
                </tr>

                <tr>
                    <td colspan="3" class="p-2">
                        <strong>FAX :</strong>
                        <input name="txtfax" wire:model="fax" size="14" />

                        <strong>Quote Needed by: </strong>
                        <input name="txtquote" wire:model="quote_by" size="10" />
                        <strong> NRE Charge: </strong>
                        <input size="3" type="text" name="necharge" wire:model.live="necharge" wire:key="nre-{{ $new_or_rep }}" @if($new_or_rep == "Repeat Order") readonly @endif>
                        Select Misc :
                        <select name="txtmisc" wire:model="selectedMisc" onchange="getmisc();"
                            wire:change="showMiscField">
                            <option value="">--select MISC--</option>
                            <option value="m1">Misc 1</option>
                            <option value="m2">Misc 2</option>
                            <option value="m3">Misc 3</option>
                        </select>
                        &nbsp;
                        <input type="checkbox" id="simplequote" name="simplequote" wire:model.live="simplequote" />
                        Simple Quote
                        <br />

                        <table width='100%' border='0'>
                            <tr>
                                <td>
                                    @if($showMisc1)
                                    <div id="misc1">
                                        <strong>Misc Charge1:</strong>
                                        <input size="3" type="text" name="descharge" wire:model="descharge">
                                        &nbsp;Name of Misc1 :
                                        <input type="text" name="desdesc" wire:model="desdesc" />
                                    </div>
                                    @endif

                                    @if($showMisc2)
                                    <div id="misc1">
                                        <strong>Misc Charge1:</strong>
                                        <input size="3" type="text" name="descharge" wire:model="descharge">
                                        &nbsp;Name of Misc1 :
                                        <input type="text" name="desdesc" wire:model="desdesc" />
                                    </div>
                                    <div id="misc2">
                                        <strong>Misc Charge2:</strong>
                                        <input size="3" type="text" name="descharge1" wire:model="descharge1">
                                        &nbsp;Name of Misc2 :
                                        <input type="text" name="desdesc1" wire:model="desdesc1" />
                                    </div>
                                    @endif

                                    @if($showMisc3)
                                    <div id="misc1">
                                        <strong>Misc Charge1:</strong>
                                        <input size="3" type="text" name="descharge" wire:model="descharge">
                                        &nbsp;Name of Misc1 :
                                        <input type="text" name="desdesc" wire:model="desdesc" />
                                    </div>
                                    <div id="misc2">
                                        <strong>Misc Charge2:</strong>
                                        <input size="3" type="text" name="descharge1" wire:model="descharge1">
                                        &nbsp;Name of Misc2 :
                                        <input type="text" name="desdesc1" wire:model="desdesc1" />
                                    </div>
                                    <div id="misc3">
                                        <strong>Misc Charge3:</strong>
                                        <input size="3" type="text" name="descharge2" wire:model="descharge2">
                                        &nbsp;Name of Misc3 :
                                        <input type="text" name="desdesc2" wire:model="desdesc2" />
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    <div id='comments' @if($simplequote==false) style="visibility:hidden;" @endif>
                                        <br><b>Comments:</b><br>
                                        <textarea name="comments" wire:model="comments" rows="3" cols="30"></textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            @if($simplequote == false)
            <div id='complexform'>
                <table width="100%" class="border-primary" border="1" cellpadding="1" cellspacing="1"
                    bordercolor="#e6e6e6" class="up" style="left:35px;">
                    <tr>
                        <td class="p-2">
                            <strong><br />Cancellation</strong>
                            <input type="radio" name="cancharge" wire:model="cancharge" value="yes"> Yes
                            <input type="radio" name="cancharge" wire:model="cancharge" value="no"> No
                            <strong>Charge</strong>
                            <input name="ccharge" wire:model="ccharge" size="8" />

                            <strong>FOB:</strong>
                            <select name="fob" wire:model="fob">
                                <option value="Anaheim">Anaheim</option>
                                <option value="Customer Dock">Customer Dock</option>
                                <option value="Factory">Factory</option>
                                <option value="Hong Kong">Hong Kong</option>
                                <option value="Other">Other</option>
                            </select>
                            @if($fob === 'Other')
                            <div id="fob_oth">Other: <input type="text" name="fob_oth" wire:model="fob_oth" size="15"
                                    maxlength='50'></div>
                            @endif

                            <br><br><strong>Vendor:</strong>
                            <select name="vid" wire:model="vid">
                                <option value=''>Select Vendor</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->data_id }}">{{ $vendor->c_name }}</option>
                                @endforeach
                                <option value='9999'>Other</option>
                            </select>
                            @if($vid === '9999')
                            <div id="vid_oth">Other: <input type="text" name="vid_oth" wire:model="vid_oth" size="30"
                                    maxlength='100'></div>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">
                            <strong>Qty(s) : </strong>
                            <!-- Quantities -->
                            @foreach(range(1, 10) as $i)
                            <input type="text" wire:model="quantities.{{ $i }}" size="3" class="mr-2">
                            @endforeach

                            <!-- Days --> <br /> <br />
                            <strong>Days(s) : </strong>
                            @foreach(range(1, 5) as $i)
                            <input type="text" wire:model="days.{{ $i }}" size="3" class="mr-2">
                            @endforeach

                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">

                            <!-- Manual Price Button -->
                            <button type="button" wire:click="toggleManualPrice" class="btn btn-primary mb-4">
                                Manual Price
                            </button>

                            <!-- Manual Price Inputs Table -->
                            @if($manualMode)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Qty \ Days</th>
                                            @foreach(range(1, 20) as $dayIndex)
                                            @if(!empty($days[$dayIndex]))
                                            <th>{{ $days[$dayIndex] }} Days</th>
                                            @endif
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(range(1, 20) as $qtyIndex)
                                        @if(!empty($quantities[$qtyIndex]))
                                        <tr>
                                            <td>{{ $quantities[$qtyIndex] }} pieces</td>
                                            @foreach(range(1, 20) as $dayIndex)
                                            @if(!empty($days[$dayIndex]))
                                            <td>
                                                <input type="text"
                                                    wire:model="manualPrices.{{ $qtyIndex }}.{{ $dayIndex }}"
                                                    class="form-control form-control-sm">
                                            </td>
                                            @endif
                                            @endforeach
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif

                            <br>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table width='100%'>
                                <tr>
                                    <td colspan='2' height="25" bgcolor='#336699' align="Left">
                                        <font color="white"><strong id='labelcomments'>ADMIN INSTRUCTIONS:</strong>
                                        </font>
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td height="25" width='50%'>
                                        <textarea name="txtinstadmin" wire:model="special_instadmin" cols="45"
                                            rows="5"></textarea>
                                    </td>

                                    <td height="25">
                                        <strong>Replace with Comments</strong> <br>
                                        Yes
                                        <input name="admcmntstat" type="radio" wire:model="is_spinsadmact"
                                            value="yes" />
                                        <br>
                                        No &nbsp;<input name="admcmntstat" type="radio" wire:model="is_spinsadmact"
                                            checked value="no" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table class="upTT" width="100%" border="1" style="border-color: #336699; left:35px;" cellpadding="1"
                    cellspacing="0" bordercolor="#e6e6e6">
                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Number of Layers : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Material Required : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>IPC Class:</strong> 1
                                <input name="chki1" type="radio" wire:model="ipc_class" value="1" /> 2
                                <input name="chki1" type="radio" wire:model="ipc_class" value="2" /> 3
                                <input name="chki1" type="radio" wire:model="ipc_class" value="3" />
                            </font>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">
                            Single Sided <input name="chk1" type="radio" wire:model="no_layer" value="single" />
                            Double Sided <input name="chk1" type="radio" wire:model="no_layer" value="Double Sided" />
                            <br /><br />
                            <strong>Multilayer: </strong>4Lyr
                            <input name="chk1" type="radio" wire:model="no_layer" value="4Lyrs" /> 6Lyr
                            <input name="chk1" type="radio" wire:model="no_layer" value="6Lyrs" /> 8Lyr
                            <input name="chk1" type="radio" wire:model="no_layer" value="8Lyrs" /> 10Lyr
                            <input name="chk1" type="radio" wire:model="no_layer" value="10Lyrs" />
                            <br /><br />
                            Other :
                            <input name="chk1" type="radio" wire:model="no_layer" value="Other" />
                            <input type="text" name="txtother1" wire:model="txtother1" />
                        </td>

                        <td height="25" class="p-2">
                            FR-4 <input name="chk7" type="radio" wire:model="m_require" value="FR-4"
                                onclick="clearval('txtother2')" />
                            &nbsp;
                            FR-4/170TG Min <input name="chk7" type="radio" wire:model="m_require" value="FR-4/170TG Min"
                                onclick="clearval('txtother2')" />
                            <br /><br />
                            Rogers 4003 <input name="chk7" type="radio" wire:model="m_require" value="Rogers 4003"
                                onclick="clearval('txtother2')" />
                            &nbsp;
                            Other: <input name="chk7" type="radio" wire:model="m_require" value="Other" />
                            <input name="txtother2" wire:model="txtother2" type="text" size="12" />
                            <br /><br />

                            <strong>Inner Copper Oz: </strong>N/A
                            <input name="chk18" type="radio" wire:model="inner_copper" value="N/A" />
                            &nbsp; .5
                            <input name="chk18" type="radio" wire:model="inner_copper" value=".5" />
                            &nbsp; 1
                            <input name="chk18" type="radio" wire:model="inner_copper" value="1" />
                            &nbsp; 2
                            <input name="chk18" type="radio" wire:model="inner_copper" value="2" />
                            <br />
                            Other <input name="chk18" type="radio" wire:model="inner_copper" value="Other" />
                            <input name="txtother6" wire:model="txtother6" type="text" size="10" /> Oz
                        </td>

                        <td height="25" valign="top" class="p-2">
                            <strong>Thickness:</strong> 0.031&quot;
                            <input name="chk13" type="radio" wire:model="thickness" value="0.031"
                                onclick="clearval('txtother4')" />
                            &nbsp; 0.062&quot;
                            <input name="chk13" type="radio" wire:model="thickness" value="0.062"
                                onclick="clearval('txtother4')" />
                            <br /><br />
                            0.093&quot;
                            <input name="chk13" type="radio" wire:model="thickness" value="0.093"
                                onclick="clearval('txtother4')" />
                            &nbsp; Other:
                            <input name="chk13" type="radio" wire:model="thickness" value="Other" />
                            <input name="txtother4" wire:model="txtother4" type="text" size="5" />
                            <br />
                            <br />
                            <strong>Thickness Tolerance:</strong>
                            <br />
                            <br />
                            +-10% <input name="chk17" type="radio" wire:model="thickness_tole" value="+/- 10%" />
                            Other <input name="chk17" type="radio" wire:model="thickness_tole" value="Other" />
                            <input name="txtother5" wire:model="txtother5" type="text" size="5" />
                        </td>
                    </tr>

                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Plate : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Design Type/Requirements : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Design Technology : </font></strong>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" width="307px" class="p-2">
                            <strong>External Cu Oz:</strong> 0.5
                            <input name="chk10" type="radio" wire:model="start_cu" value="0.5" />
                            &nbsp; 1
                            <input name="chk10" type="radio" wire:model="start_cu" value="1" />
                            &nbsp; 2
                            <input name="chk10" type="radio" wire:model="start_cu" value="2" />
                            <br />
                            <br />
                            Other :
                            <input name="chk10" type="radio" wire:model="start_cu" value="Other" />
                            <input type="text" name="txtother3" wire:model="txtother3" /> Oz
                            <br />
                            <br />
                            <br />
                            <strong>Plated Cu in Holes (Min.):</strong>
                            .0010 <input name="chk22" type="radio" wire:model="plated_cu" value=".0010" />
                            &nbsp; .0014 <input name="chk22" type="radio" wire:model="plated_cu" value=".0014 " />
                            &nbsp;&nbsp;&nbsp;&nbsp;1oz <input name="chk22" type="radio" wire:model="plated_cu"
                                value="1oz " />
                            &nbsp; Other <input name="chk22" type="radio" wire:model="plated_cu" value="Other" />
                            <input name="txtother7" wire:model="txtother7" type="text" size="5" />
                            <br />
                            <br />
                            <strong>Fingers Nickel/Hard Gold: </strong>
                            Yes <input name="chk25" type="checkbox" wire:model="fingers_gold" value="yes" />
                        </td>

                        <td height="25" width="330px" class="p-2" valign="top">
                            <strong>Trace Min. = </strong>.006
                            <input name="chk27" type="radio" wire:model="trace_min" value=".006"
                                onclick="clearval('txtother54')" />
                            .005 <input name="chk27" type="radio" wire:model="trace_min" value=".005"
                                onclick="clearval('txtother54')" />
                            .004 <input name="chk27" type="radio" wire:model="trace_min" value=".004"
                                onclick="clearval('txtother54')" />
                            .003 <input name="chk27" type="radio" wire:model="trace_min" value=".003"
                                onclick="clearval('txtother54')" />
                            <br />Other <input name="chk27" type="radio" wire:model="trace_min" value="Other" />
                            <input name="txtother54" wire:model="txtother54" type="text" size="10" />
                            <br />
                            <br />
                            <strong>Space Min. =</strong>.006
                            <input name="chk31" type="radio" wire:model="space_min" value=".006"
                                onclick="clearval('txtother55')" />
                            .005 <input name="chk31" type="radio" wire:model="space_min" value=".005"
                                onclick="clearval('txtother55')" />
                            .004 <input name="chk31" type="radio" wire:model="space_min" value=".004"
                                onclick="clearval('txtother55')" />
                            .003 <input name="chk31" type="radio" wire:model="space_min" value=".003"
                                onclick="clearval('txtother55')" />
                            <br />
                            Other <input name="chk31" type="radio" wire:model="space_min" value="Other" />
                            <input name="txtother55" wire:model="txtother55" type="text" size="10" />
                            <br />
                            <br />
                            <strong>Controlled Impedance:</strong>
                            <input name="chk35" type="checkbox" wire:model="con_impe_sing" value="Yes" /> Yes
                            <br />
                            <br />
                            Single Ended <input name="chk109" type="checkbox" wire:model="con_impe_sing"
                                value="Single Ended" />
                            Differential <input name="chk110" type="checkbox" wire:model="con_impe_diff"
                                value="Differential" />
                            <br />
                            <br />
                            <strong>Impedance Tolerance:</strong>
                            <br />+-10% <input name="chk202" type="radio" wire:model="tore_impe" value="+/- 10%" />
                            Other <input name="chk202" type="radio" wire:model="tore_impe" value="Other" />
                            <input name="txtother51" wire:model="txtother51" type="text" size="10" />
                        </td>

                        <td height="25" valign="top" class="p-2">
                            <strong>Smallest Hole Size:</strong>
                            <input name="txtother8" wire:model="hole_size" type="text" size="8" />
                            <br /><br />
                            <strong>Smallest Pad:</strong>
                            <input name="txtother19" wire:model="pad" type="text" size="10" />
                            <br />
                            <br />
                            <strong>Blind Vias:</strong>
                            <input name="chk37" type="checkbox" wire:model="blind" value="yes" /> Yes
                            <br />
                            <br />
                            <strong>Buried Vias: </strong>
                            <input name="chk39" type="checkbox" wire:model="buried" value="yes" /> Yes
                            <br />
                            <br />
                            <strong>HDI Design:</strong>
                            <input name="chk200" type="checkbox" wire:model="hdi_design" value="Yes" /> Yes
                            <br />
                            <br />
                            <strong>Non-Conductive Filled/Plated Over:</strong><br>
                            <input name="chk215" type="checkbox" wire:model="resin_filled" value="Yes" /> Yes
                            <br />
                            <strong>Conductive Filled Vias:</strong>
                            <input name="chk210" type="checkbox" wire:model="cond_vias" value="Yes" /> Yes
                        </td>
                    </tr>

                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Finish : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Solder Resist : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Nomenclature : </font></strong>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">
                            <strong>Finish:</strong> HASL
                            <input name="chk43" type="radio" wire:model="finish" value="HASL" />
                            &nbsp; Lead-Free HASL
                            <input name="chk43" type="radio" wire:model="finish" value="Lead-Free HASL" />
                            <br />
                            <br />
                            ENIG <input name="chk43" type="radio" wire:model="finish" value="ENIG" />
                            &nbsp; Imm.Silver
                            <input name="chk43" type="radio" wire:model="finish" value="Imm.Silver" />
                            &nbsp; Imm.Tin
                            <input name="chk43" type="radio" wire:model="finish" value="Imm.Tin" />
                            <br />
                            <br />
                            Other : <input name="chk43" type="radio" wire:model="finish" value="Other" />
                            <input name="txtother9" wire:model="txtother9" type="text" size="15" />
                        </td>

                        <td height="25" class="p-2">
                            <strong>Mask Sides:</strong> N/A
                            <input name="chk48" type="radio" wire:model="mask_size" value="N/A" />
                            &nbsp; 1
                            <input name="chk48" type="radio" wire:model="mask_size" value="1" />
                            &nbsp; Both
                            <input name="chk48" type="radio" wire:model="mask_size" value="Both" />
                            <br />
                            <br />
                            <strong>Color:</strong> Green
                            <input name="chk51" type="radio" wire:model="color" value="Green"
                                onclick="clearval('txtother10')" />
                            &nbsp; Blue
                            <input name="chk51" type="radio" wire:model="color" value="Blue"
                                onclick="clearval('txtother10')" />
                            <br />
                            <br />
                            Other : <input name="chk51" type="radio" wire:model="color" value="Other" />
                            <input name="txtother10" wire:model="txtother10" type="text" size="15" />
                            <br />
                            <br />
                            <strong>Mask Type:</strong> Glossy
                            <input name="chk53" type="radio" wire:model="mask_type" value="Glossy" />
                            &nbsp; Matte
                            <input name="chk53" type="radio" wire:model="mask_type" value="Matte " />
                        </td>

                        <td height="25" width="273px" class="p-2">
                            <strong>S/S Sides: </strong>N/A
                            <input name="chk55" type="radio" wire:model="ss_side" value="N/A" />
                            &nbsp; 1
                            <input name="chk55" type="radio" wire:model="ss_side" value="1" />
                            &nbsp; 2
                            <input name="chk55" type="radio" wire:model="ss_side" value="2" />
                            <br />
                            <br />
                            <strong>S/S Color: </strong>
                            White <input name="chk58" type="radio" wire:model="ss_color" value="White"
                                onclick="clearval('txtother11')" />
                            Black <input name="chk58" type="radio" wire:model="ss_color" value="Black"
                                onclick="clearval('txtother11')" />
                            Yellow <input name="chk58" type="radio" wire:model="ss_color" value="Yellow"
                                onclick="clearval('txtother11')" />
                            <br />
                            <br />
                            <strong>Other:</strong>
                            <input name="chk58" type="radio" wire:model="ss_color" value="Other" />
                            <input name="txtother11" wire:model="txtother11" type="text" size="15" />
                        </td>
                    </tr>

                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Fabrication : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Array Requirements : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Special Call-Outs : </font></strong>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" class="p-2">
                            <strong>Board Size (in.)</strong>
                            <input name="txtother12" wire:model="board_size1" type="text" size="5" /> X
                            <input name="txtother13" wire:model="board_size2" type="text" size="5" />
                            <br />
                            <br />
                            <strong>Array:</strong>
                            <input name="chk63" type="checkbox" wire:model="array" value="YES" /> Yes
                            <br /><br />

                            <div id="yes_box2" style="display: block; ">
                                <strong>Bds Per Array:</strong>
                                <input name="txtother14" wire:model="b_per_array" type="text" size="4" />
                                <br /><br />
                                <strong>Array Size:</strong>
                                <input name="txtother15" wire:model="array_size1" type="text" size="5" /> X
                                <input name="txtother16" wire:model="array_size2" type="text" size="5" />
                            </div>
                            <br />
                            <strong>Rout Tolerance:</strong> +/-.005
                            <input name="chk204" type="radio" wire:model="route_tole" value="+/-.005"
                                onclick="clearval('txtother53')" />
                            Other <input name="chk204" type="radio" wire:model="route_tole" value="Other" />
                            <input name="txtother52" wire:model="txtother52" type="text" size="2" />
                        </td>

                        <td height="25" class="p-2">
                            <strong>Array Design Provided:</strong>
                            <input name="chk65" type="checkbox" wire:model="array_design" value="Yes" /> Yes
                            <br /><br />
                            <strong>Factory to Design Array: </strong>
                            <input name="chk67" type="checkbox" wire:model="design_array" /> Yes
                            <br /><br />
                            <strong>Array Type:</strong> Tab Route
                            <input name="chk69" type="checkbox" wire:model="array_type1" value="Tab Route" />
                            &nbsp; V Score
                            <input name="chk70" type="checkbox" wire:model="array_type2" value="V Score" />
                            <br /><br />
                            <strong>Array Requires: </strong>Tooling Holes
                            <input name="chk72" type="checkbox" wire:model="array_require1" value="Tooling Holes" />
                            <br /><br />
                            Fiducials <input name="chk73" type="checkbox" wire:model="array_require2"
                                value="Fiducials" />
                            &nbsp; Mousebites
                            <input name="chk74" type="checkbox" wire:model="array_require3" value="Mousebites" />
                        </td>

                        <td height="25" class="p-2">
                            <strong>Milling: </strong>
                            <input name="chk75" type="checkbox" wire:model="bevel" value="yes" /> Yes
                            <br />
                            <br />
                            <strong>Countersink:</strong>
                            <input name="chk77" type="checkbox" wire:model="counter_sink" /> Yes
                            <br />
                            <br />
                            <strong>Control Depth:</strong>
                            <input name="chk79" type="checkbox" wire:model="cut_outs" /> Yes
                            <br />
                            <br />
                            <strong>Edge Plating:</strong>
                            <input name="chk81" type="checkbox" wire:model="slots" value="Yes" /> Yes
                        </td>
                    </tr>

                    <tr>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Markings : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>QA Requirements : </font></strong>
                        </td>
                        <td height="25" bgcolor='#336699'>
                            <font color="white"><strong>Miscellaneous : </font></strong>
                        </td>
                    </tr>

                    <tr>
                        <td height="25" width="307px" class="p-2">
                            <strong>Logo:</strong>
                            Factory <input name="chk83" type="radio" wire:model="logo" value="Factory"
                                onclick="clearval('txtother56')" />
                            Other <input name="chk83" type="radio" wire:model="logo" value="Other" />
                            <input name="txtother56" wire:model="txtother56" type="text" size="3" /> Logo
                            <br />
                            <br />
                            <strong>94V-0 Mark: </strong>
                            <input name="chk85" type="checkbox" wire:model="mark" value="Yes" /> Yes
                            <br />
                            <br />
                            <strong>Date Code Format:</strong>
                            WWYY <input name="chk87" type="radio" wire:model="date_code" value="WWYY" />
                            &nbsp; YYWW <input name="chk87" type="radio" wire:model="date_code" value="YYWW" />
                            &nbsp;
                            <strong>Other Marking: </strong>
                            <input name="chk87" type="radio" wire:model="date_code" value="Other Marking " />
                            <input name="txtother17" wire:model="txtother17" type="text" size="10" /> D/C Format
                        </td>

                        <td height="25" class="p-2">
                            <strong>Microsection Required:</strong>
                            <input name="chk90" type="checkbox" wire:model="micro_section" value="YES" /> Yes
                            <br />
                            <br />
                            <strong>Electrical Test Stamp: </strong>
                            <input name="chk92" type="checkbox" wire:model="test_stamp" value="Yes" /> Yes
                            <br />
                            <br />
                            In Board <input name="chk94" type="checkbox" wire:model="in_board" value="In Board" />&nbsp;
                            In Array Rail <input name="chk199" type="checkbox" wire:model="array_rail"
                                value="In Array Rail" />
                        </td>

                        <td height="25" class="p-2">
                            <strong>X-Outs Allowed:</strong>
                            <input name="chk95" type="checkbox" wire:model="xouts" onClick="xoutsnot();" />
                            Yes
                            <br />
                            <br />
                            <strong># of X-outs per Array:</strong>
                            <input name="txtother28" wire:model="xoutsnum" type="text" size="4" />
                            <br /><br />
                            <strong>RoHS Cert:</strong>
                            <input name="chk97" type="checkbox" wire:model="rosh_cert" /> Yes
                        </td>
                    </tr>
                </table>
            </div>
            @endif
            <table class="upTT" width="100%" border="1" style="border-color: #336699; left:35px;" cellpadding="1"
                cellspacing="0" bordercolor="#e6e6e6">
                <tr>
                    <td height="25" colspan="3">
                        <div id='specialreq' style='color: #000; font: 11px/25px Verdana;'></div>
                    </td>
                </tr>

                <tr>
                    <td height="25" colspan="3" class="p-3">
                        <input type="button" class="btn btn-sm btn-primary @if($button_status == 1) disabled @endif" wire:click="save" value="Submit">
                        &nbsp;
                        <label><input type="reset" class="btn btn-sm btn-warning" name="button2"
                                value="Reset" /></label>
                        &nbsp;&nbsp;Receive reminders
                        <input type="checkbox" wire:model="reminders"> after every
                        <input type="text" name="days" wire:model="day" size='2' maxlength='3' value="15"> days
                    </td>
                </tr>

                <tr>
                    <td height="25" colspan="3">&nbsp;</td>
                </tr>
            </table>
        </form>
    </div>
       <!-- Alert Modal -->
    <div class="modal fade @if($showAlertPopup) show d-block @endif" id="alertModal" tabindex="-1"
        style="@if($showAlertPopup) display: block; @endif" role="dialog">
        <div class="modal-dialog modal-dialog-centered draggable-modal" style="max-width: 500px;">
            <div class="modal-content" style="background: #ccffff; border: 1px solid #999; font-size: 13px;">
                <div class="modal-header py-2 px-3 modal-drag-handle"
                    style="background: transparent; border-bottom: 1px solid #999; cursor: move;">
                    <label class="modal-title fw-bold text-dark m-0" style="font-size: 18px;">
                        <i class="fa fa-bell"></i> Part no Alerts</label>
                    <button type="button" class="btn btn-link text-danger p-0" style="font-size: 13px;"
                        wire:click="closeAlertPopup">Close</button>
                </div>

                <div class="modal-body pt-2 px-3">
                    @if(!empty($alertMessages))
                        @php 
                        $count = 1;
                        @endphp
                        @foreach($alertMessages as $index => $message)
                            <div class="pb-1 mb-1 border-bottom" wire:key="alert-{{ $message->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $count++ }}.</strong>
                                        <span style="font-size: 13px;">{{ $message->alert }}</span>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm text-primary" style="font-size: 12px;"
                                            wire:click="editAlert({{ $message->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm text-danger" style="font-size: 12px;"
                                            wire:click="deleteAlert({{ $message->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted mb-3" style="font-size: 13px;">No alerts found.</div>
                    @endif

                    <div class="mt-2 mb-2">
                        <label class="form-label small mb-1">
                            @if($editingAlertId)
                                Edit Alert
                            @else
                                Add New Alert
                            @endif
                        </label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" value="{{ $newAlert }}" wire:model="newAlert"
                                style="pointer-events: auto;"> <!-- Ensure input is always clickable -->
                            <br />
                            @error('newAlert')
                                <font color="red">{{ $message }}</font>
                            @enderror
                            @if($editingAlertId)
                                <button class="btn btn-success" wire:click.prevent="updateAlert">Update</button>
                                <button class="btn btn-secondary" wire:click="cancelEdit">Cancel</button>
                            @else
                                <button class="btn btn-outline-dark" wire:click="addAlert">Add Alert</button>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['quo' => 'Quote','po' => 'Purchase Order','con' => 'Confirmation', 'pac' => 'Packing', 'inv' => 'Invoice', 'cre' => 'Credit'] as $value => $label)
                            <div class="form-check form-check-inline mb-0" style="margin-right: 0;">
                                <input type="checkbox" class="form-check-input" id="type-{{ $value }}" value="{{ $value }}"
                                    wire:model="alertTypes"
                                    wire:key="alert-type-{{ $value }}-{{ $editingAlertId ?? 'new' }}">
                                <label class="form-check-label" for="type-{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                        @error('alertTypes')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade @if($showProfilePopup) show d-block @endif" id="profileModal" tabindex="-1"
        style="@if($showProfilePopup) display: block; @endif" role="dialog">
        <div class="modal-dialog modal-dialog-centered draggable-modal" style="max-width: 400px; width: 50%;">
            <div class="modal-content" style="background: #f0f8ff; border: 1px solid #999; font-size: 13px;">
                <div class="modal-header py-2 px-3 modal-drag-handle"
                    style="background: transparent; border-bottom: 1px solid #999; cursor: move;">
                    <label class="modal-title fw-bold text-dark m-0" style="font-size: 16px;">
                        <i class="fa fa-user-circle"></i> Customer Profile Requirements</label>
                    <button type="button" class="btn btn-link text-danger p-0" style="font-size: 13px;"
                        wire:click="closeProfilePopup">Close</button>
                </div>

                <div class="modal-body pt-2 px-4" style="max-height: 70vh; overflow-y: auto;">
                    @if(!empty($profileMessages))
                        @foreach($profileMessages as $profile)
                            <div class="mb-3">
                                @foreach($profile->details as $detail)
                                    @if(str_contains($detail->viewable, 'cre'))
                                        <div class="pb-2 mb-2 border-bottom">
                                            <div class="d-flex justify-content-between">
                                                <div style="width: 95%;">
                                                    <strong>{{ $loop->iteration }}.</strong>
                                                    <span style="font-size: 13px; word-wrap: break-word;">{{ $detail->reqs }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted mb-3" style="font-size: 13px;">No profile requirements found.</div>
                    @endif
                </div>
            </div>
        </div>
        </div>

        <style>
            .modal {
                z-index: 1040;
                background-color: transparent;
                pointer-events: none;
                /* Allow clicks to pass through modal container */
            }

            .modal.show {
                z-index: 1050;
                display: block;
            }

            .draggable-modal {
                position: fixed;
                margin: 0;
                z-index: 1050;
                pointer-events: auto;
                /* Enable interactions within modal */
            }

            .mod al-drag-handle {
                cursor: move;
            }

            /* Ensure all interactive elements are clickable */
            .modal-content * {
                pointer-events: auto;
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let zIndexCounter = 1050;

                // Initializ  e interact.js for draggable modals
                interact('.d raggable-modal').draggable({
                    allo  wFrom: '.modal-drag-handle',
                    ignoreFrom: 'button, input, a, .btn, [wire\\:click], [wire\\:model]',
                    modifiers: [
                        interact.modifiers.restrictRect({
                            restriction: 'parent',
                            endOnly: true
                        })
                    ],
                    listener  s: {
                        start(event) {
                            bringToFront(event.target);
                        },
                        move(event) {
                            const target = event.target;
                            const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                            const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                            target.style.transform = `translate(${x}px, ${y}px)`;
                            target.setAttribute('data-x', x);
                            target.setAttribute('data-y', y);
                        }
                    }
                });

                function bringToFront(modal) {
                    zIndexCounter++;
                    modal.style.zIndex = zIndexCounter;
                }

                // Cente    r modals when they appear
                function centerModal(modalId) {
                    const modal = document.querySelector(`#${modalId} .draggable-modal`);
                    if (modal) {
                        const windowWidth = window.innerWidth;
                        const windowHeight = window.innerHeight;
                        const modalWidth = modal.offsetWidth;
                        const modalHeight = modal.offsetHeight;

                        modal.style.left = `${(windowWidth - modalWidth) / 2}px`;
                        modal.style.top = `${(windowHeight - modalHeight) / 2}px`;
                        modal.style.transform = 'translate(0px, 0px)';
                        modal.setAttribute('data-x', 0);
                        modal.setAttribute('data-y', 0);

                        bringToFront(modal);
                    }
                }

                // L   ivewire event listeners
                document.addEventListener('livewire:init', () => {
                    Livewire.on('alert-types-updated', () => {
                        document.querySelectorAll('[wire\\:model="alertTypes"]').forEach(checkbox => {
                            checkbox.checked = checkbox.value.includes(checkbox.value);
                        });
                    });

                    Livewire.on('showAlertPopup', () => {
                        centerModal('alertModal');
                    });

                    Livewire.on('showProfilePopup', () => {
                        centerModal('profileModal');
                    });
                });

                // Initial centering if modals are already visible
                if (document.querySelector('#alertModal.show')) {
                    centerModal('alertModal');
                }
                if (document.querySelector('#profileModal.show')) {
                    centerModal('profileModal');
                }
            });
            // for alert edit 
            document.addEventListener('livewire:load', function () {
                Livewire.on('alert-types-updated', () => {
                    // Force re-render checkboxes
                    document.querySelectorAll('[wire\\:model="alertTypes"]').forEach(checkbox => {
                        checkbox.checked = @json($this->alertTypes).includes(checkbox.value);
                    });
                });
            });
            document.addEventListener('livewire:init', function () {
                // Force checkbox updates when Livewire finishes rendering
                Livewire.on('alert-types-updated', () => {
                    setTimeout(() => {
                        document.querySelectorAll('[wire\\:model="alertTypes"]').forEach(checkbox => {
                            const shouldBeChecked = @this.alertTypes.includes(checkbox.value);
                            checkbox.checked = shouldBeChecked;
                            checkbox.dispatchEvent(new Event('change'));
                        });
                    }, 50);
                });
            });
        </script>
</div>