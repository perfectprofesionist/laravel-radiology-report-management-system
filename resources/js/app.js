import "./bootstrap";
import "laravel-datatables-vite";
import Swal from "sweetalert2";

import $ from "jquery";
import "jquery-ui/ui/widgets/datepicker";
import "jquery-ui/themes/base/all.css";
import "dropzone/dist/dropzone.css";

import { Dropzone } from "dropzone";


$(document).ready(function () {


    console.log("test run");
    Dropzone.autoDiscover = false;

    let relativePath = "";

    const dropzoneElement = document.querySelector("div#dropzone");
    console.log('Dropzone element found:', dropzoneElement);

    if (dropzoneElement) {
        const myDropzone = new Dropzone(dropzoneElement, {
            url: "/upload-advanced", // Update with your actual upload route
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 1500, // Max file size in MB (1.5GB)
            acceptedFiles: ".jpg,.jpeg,.png,.pdf,.dcm", // Accept DICOM and common file types
            addRemoveLinks: true,
            dictDefaultMessage: "Drop files here or click to upload",
            dictRemoveFile: "Remove File",
            maxFiles: 1, // Limit to 1 file
            chunking: true, // Enable chunking
            chunkSize: 10485760, // 10MB chunks for better reliability with 1GB files
            forceChunking: true, // Force chunking even if the file is smaller than the chunk size
            retryChunks: true, // Retry failed chunks
            retryChunksLimit: 3, // Number of retry attempts
            parallelChunkUploads: true, // Upload chunks in parallel
            timeout: 300000, // Increased timeout for large files (5 minutes)
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"), // Add CSRF token here
            },
            init: function () {
                const placeholder = document.getElementById("dropzone-placeholder");
                this.on("addedfile", function (file) {
                    // Automatically remove the previous file
                    if (this.files.length > 1) {
                        this.removeFile(file);
                        Swal.fire({
                            icon: "warning",
                            title: "Only one file allowed",
                            text: "Please remove the existing file before adding a new one.",
                        });
                    } else {
                        if (placeholder) placeholder.style.display = "none"; // hide placeholder
                    }
                });
                this.on("success", function (file, response) {
                    console.log("Upload added:", response);
                    relativePath = response.path + response.name;
                    document.querySelector("#scan_file").value = relativePath;
                });
                this.on("removedfile", function (file) {
                    if (this.files.length === 0 && placeholder) {
                        placeholder.style.display = "inline"; // show placeholder again
                    }
                    if (file.xhr) {
                        console.log("File removed:", file);
                        console.log("File path to remove:", relativePath);

                        axios.post("/remove-uploaded-file", {
                                file_path: relativePath,
                            })
                            .then((response) => {
                                if (response.data.success) {
                                    console.log("file removed from the server as well");
                                    document.querySelector("#scan_file").value = "";
                                } else {
                                    console.error("Error removing file:", error);
                                }
                            })
                            .catch((error) => {
                                console.error("Error removing file:", error);
                            });
                    }
                });

                this.on("error", function (file, errorMessage, xhr) {
                    console.log("Error uploading file:", errorMessage);
                    Swal.fire({
                        icon: "error",
                        title: "Upload Error",
                        text: errorMessage
                    });
                });
                this.on("uploadprogress", function (file, progress) {
                    console.log("Upload progress: ", progress);
                });
                this.on("chunksuccess", function(file, chunk) {
                    console.log(`Chunk ${chunk.index + 1} of ${Math.ceil(file.size / this.options.chunkSize)} uploaded`);
                });
            },
        });
    }


    // console.log('test.1');
    $("#patient_dob").datepicker({
        dateFormat: "dd-mm-yy", // Change format if needed
    });

    $("#scan_date").datepicker({
        dateFormat: "dd-mm-yy", // Change format if needed
    });

    $("#modality").change(function () {
        let selectedOption = $(this).find(":selected");
        let price = selectedOption.data("price");
        let modality_id = selectedOption.val();

        $("#priceValue").text(price);
        $("#priceAmount").text(price);
        $("#priceSection").show();
        $("#card-section").show(); // show card field
        $("#scanUploadButton").prop("disabled", true); // disable until payment

        // Store price + modality globally
        $("#payNowBtn").data("price", price).data("modality", modality_id);
    });
});
