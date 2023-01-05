<style>
        .inputtext:focus + *{
            transform: translateY(-50%);
            color: #3b82f6;
            font-size: 11px;
        }
        body::before {
            content: "";
            position: fixed;
            top: -10px;
            left: 0;
            width: 100%;
            height: 10px;
            box-shadow: 0px 0 10px rgba(0, 0, 0, 0.8);
            z-index: 100;
        }
        #barContainer{
            -webkit-box-shadow: 0px -5px 19px -8px rgba(120,120,120,1);
            -moz-box-shadow: 0px -5px 19px -8px rgba(120,120,120,1);
            box-shadow: 0px -5px 19px -8px rgba(120,120,120,1);
        }
        #datalokasi_length{
            margin-bottom: 10px;
            float: none;
            text-align: left;
        }
        #datalokasi_length label select{
            border: 1px solid #3b82f6;
        }
        #datalokasi_length label select:focus{
            outline: none;
            border: 1px solid #1e40af;
        }
        div #datalokasi_paginate{
            float: none;
            margin-right: auto;
            text-align: left;
            margin-bottom: 10px;
            margin-top: 10px;
        }
        div #datalokasi_paginate .paginate_button.previous{
            margin-left: 0px;   
            padding-left: 0px;   
        }
        div #datalokasi_info{
            float: none;
            text-align: left;
        }
        div #datalokasi_filter{
            float: none;
            text-align: left;
            margin-bottom: 10px;
            display: block;
        }
        div #datalokasi_filter label input{
            border: 1px solid #3b82f6;
        }
        div #datalokasi_filter label input:focus{
            outline: none;
            border: 1px solid #1e40af;
        }

        #kategoritable_length{
            margin-bottom: 10px;
            float: none;
            text-align: left;
        }
        #kategoritable_length label select{
            border: 1px solid #3b82f6;
        }
        #kategoritable_length label select:focus{
            outline: none;
            border: 1px solid #1e40af;
        }
        div #kategoritable_paginate{
            float: none;
            margin-right: auto;
            text-align: left;
            margin-bottom: 10px;
            margin-top: 10px;
        }
        div #kategoritable_paginate .paginate_button.previous{
            margin-left: 0px;   
            padding-left: 0px;   
        }
        div #kategoritable_info{
            float: none;
            text-align: left;
        }
        div #kategoritable_filter{
            float: none;
            text-align: left;
            margin-bottom: 10px;
            display: block;
        }
        div #kategoritable_filter label input{
            border: 1px solid #3b82f6;
        }
        div #kategoritable_filter label input:focus{
            outline: none;
            border: 1px solid #1e40af;
        }
</style>