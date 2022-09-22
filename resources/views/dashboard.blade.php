@include('includes.head')
@include('includes.user-header')

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
        <div class="main-content">

            <div class="section__content section__content--p30">
                <div class="container-fluid">

                    <section>
                        <h3 class="sumb--title">User Dashboard</h3>
                    </section>

                    <section>

                        <div class="sumb--statistics row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <a href="#" class="sumb--dashlinkbox sumb--putShadowbox statistic__item--green">
                                    <div class="sumb-statistic__item">
                                        <h2>13</h2>
                                        <span>Accountant</span>
                                        <div class="icon">
                                            <i class="zmdi zmdi-account-o"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <a href="#" class="sumb--dashlinkbox sumb--putShadowbox statistic__item--orange">
                                    <div class="sumb-statistic__item">
                                        <h2>3</h2>
                                        <span>Documents</span>
                                        <div class="icon">
                                            <i class="zmdi zmdi-book"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <a href="#" class="sumb--dashlinkbox sumb--putShadowbox statistic__item--blue">
                                    <div class="sumb-statistic__item">
                                        <h2>1</h2>
                                        <span>Processed Business Name, ABN, Trust and Company Registration</span>
                                        <div class="icon">
                                            <i class="zmdi zmdi-folder-star-alt"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <a href="#" class="sumb--dashlinkbox sumb--putShadowbox statistic__item--red">
                                    <div class="sumb-statistic__item">
                                        <h2>17</h2>
                                        <span>Sign up forms</span>
                                        <div class="icon">
                                            <i class="zmdi zmdi-file-text"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </section>


                    <section>
                        <div class="sumb--addtl1 row" style="margin-bottom: 0px !important;">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">

                                    <h4 class="sumb--title2">Announcements</h4>
                                    <div class="sumb--dashboardAnn sumb--putShadowbox">
                                        <ul>
                                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ullamcorper ullamcorper magna, vel convallis arcu facilisis eget.</li>
                                            <li><a href="">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</a></li>
                                        </ul>
                                    </div>

                                    <h4 class="sumb--title2">Needs Action</h4>
                                    <div class="sumb--dashboardRequest sumb--putShadowbox">
                                        <ul>
                                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                                            <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc ullamcorper ullamcorper magna, vel convallis arcu facilisis eget.</li>
                                            <li><a href="">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</a></li>
                                        </ul>
                                    </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                    <h4 class="sumb--title2">Calendar</h4>
                                    <div class="sumb--dashboardCal sumb--putShadowbox">
                                        <div id="calendar"></div>
                                    </div>

                            </div>
                        </div>
                    </section>

                    

                    <section class="m-b-10">

                        <h4 class="sumb--title2">Recent Lodgements</h4>
                        
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="sumb--recentlogdements sumb--putShadowbox">

                                    <div class="table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th style="border-top-left-radius: 7px;">date</th>
                                                    <th>order ID</th>
                                                    <th>name</th>
                                                    <th>lodgement type</th>
                                                    <th>status</th>
                                                    <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">actions</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td><a href="acct-users-details.php">UZUMAKI TRUST</a></td>
                                                    <td>Trust Registration</td>
                                                    <td class="sumb--recentlogdements__status_acc">Accepted</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-28</td>
                                                    <td>100397</td>
                                                    <td><a href="acct-users-details.php">JOSEPH TORRES</a></td>
                                                    <td>ABN Registration</td>
                                                    <td class="sumb--recentlogdements__status_proc">Being Process</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-27</td>
                                                    <td>100396</td>
                                                    <td><a href="acct-users-details.php">STARTING AT THE BOTTOM</a></td>
                                                    <td>Business Name Registration</td>
                                                    <td class="sumb--recentlogdements__status_rej">Rejected</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-26</td>
                                                    <td>100395</td>
                                                    <td><a href="acct-users-details.php">SUPER HUMANS PTY LTD</a></td>
                                                    <td>Company Registration</td>
                                                    <td class="sumb--recentlogdements__status_rev">Manual Review</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-25</td>
                                                    <td>100393</td>
                                                    <td><a href="acct-users-details.php">JANE TORRES</a></td>
                                                    <td>ABN Registration</td>
                                                    <td class="sumb--recentlogdements__status_acc">Accepted</td>
                                                    <td class="sumb--recentlogdements__actions"><a href="#"><i class="fa-solid fa-eye"></i></a></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" class="sumb--recentlogdements__tableender">
                                                        5 Recent Lodgements
                                                        <br>
                                                        <a href="#">View All Lodgements</a>
                                                    </td>
                                                </tr>

                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="p-b-20">

                        <h4 class="sumb--title2">Recent Employment Document Files</h4>
                        
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="sumb--recentlogdements sumb--putShadowbox">

                                    <div class="table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th style="border-top-left-radius: 7px;">date</th>
                                                    <th>form ID</th>
                                                    <th>employee name</th>
                                                    <th>category</th>
                                                    <th>document type</th>
                                                    <th>status</th>
                                                    <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">actions</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Probation Letter</td>
                                                    <td>Successful Probation</td>
                                                    <td class="sumb--recentlogdements__status_acc">Completed</td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <a href="#"><i class="fa-solid fa-file-arrow-down"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Probation Letter</td>
                                                    <td>Unsuccessful Probation</td>
                                                    <td class="sumb--recentlogdements__status_acc">Completed</td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <a href="#"><i class="fa-solid fa-file-arrow-down"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Employment Letter</td>
                                                    <td>Letter of Engagement</td>
                                                    <td class="sumb--recentlogdements__status_acc">Completed</td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <a href="#"><i class="fa-solid fa-file-arrow-down"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Warning Letter</td>
                                                    <td>First/Second Warning Letter</td>
                                                    <td class="sumb--recentlogdements__status_acc">Completed</td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                        <a href="#"><i class="fa-solid fa-file-arrow-down"></i></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>2018-09-29</td>
                                                    <td>100398</td>
                                                    <td>ROMEO TORRES</td>
                                                    <td>Warning Letter</td>
                                                    <td>Final Warning Letter</td>
                                                    <td class="sumb--recentlogdements__progbar">
                                                        50% Completed
                                                        <div class="__progbarWrap">
                                                            <div class="__progbarWrap--status" style="width: 50%">&nbsp;</div>
                                                        </div>
                                                    </td>
                                                    <td class="sumb--recentlogdements__actions">
                                                        <a href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    </td>
                                                </tr>
                                                
                                                <tr>
                                                    <td colspan="7" class="sumb--recentlogdements__tableender">
                                                        5 Recent Employment Document Files
                                                        <br>
                                                        <a href="#">View All Employment Document Files</a>
                                                    </td>
                                                </tr>

                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    
                </div>
            </div>
        </div>
    <!-- END MAIN CONTENT-->
</div>
<!-- END PAGE CONTAINER-->


@include('includes.footer')
</body>

</html>
<!-- end document-->