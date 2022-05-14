<!DOCTYPE html>
<html lang="en">
<head>
    <title>REDIS TO-DO</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.2/font/bootstrap-icons.css">
    <meta name="theme-color" content="#db4938" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<!-- Start To-Do Edit Modal -->
<div class="modal" id="todoEditModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <form class="row row-cols-lg-auto g-3 justify-content-center align-items-center editForm"
                      method="post">
                    <div class="col-12">
                        <div class="form-outline">
                            <input type="text" name="task" class="form-control taskEditInput"
                                   placeholder="Enter to-do item" required/>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-outline">
                            <input type="date" name="date" class="form-control dateEditInput" required/>
                        </div>
                    </div>

                    <div class="col-12">
                        <input type="hidden" name="id" value="" class="idEditInput">
                        <button type="submit" class="btn btn-primary editSubmitBtn">Save</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End To-Do Edit Modal -->

<section class="" style="background-color: #eee; min-height: 100vh">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-lg-9 col-xl-7">
                <div class="card rounded-3">
                    <div class="card-body p-4">

                        <h4 class="text-center my-3 pb-3">REDIS TO-DO</h4>

                        <form class="row row-cols-lg-auto g-3 justify-content-center align-items-center mb-4 pb-2 addForm"
                              method="post">
                            <div class="col-12">
                                <div class="form-outline">
                                    <input type="text" name="task" class="form-control task"
                                           placeholder="Enter to-do item" required/>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-outline">
                                    <input type="date" name="date" class="form-control date" required/>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary addSubmitBtn">Add</button>
                            </div>
                        </form>

                        <div class="card">
                            <div class="card-body" style="padding: 0 0 0 0">
                                <table class="table table-striped" style="margin-bottom: 0px">
                                    <tbody class="taskListingParent">
                                    </tbody>
                                    <div class="loadingImage">
                                        <img src="<?php echo site_url('assets/images/loading2.gif'); ?>"
                                             class="mx-auto d-block"
                                             style="max-width:  100px" alt="">
                                    </div>
                                    <div class="alert alert-warning noToDoAlert"
                                         style="margin-bottom: 0px; display: none" role="alert">
                                        No To-To Found! Start adding your to-do list
                                    </div>

                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    let endPoint = '<?php echo site_url('handle'); ?>';
</script>
<script src="assets/script.js"></script>
</html>
