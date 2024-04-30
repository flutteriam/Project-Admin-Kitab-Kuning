<html>

<head>
    <title>Download Book</title>
    <style>
        body {
            margin-left: 200px;
            margin-right: 200px;
        }

        .text-container {
            display: inline-block;
            margin: 10px;
            flex-direction: column;
            text-align: center;
            align-items: center;
            border: 2px solid #898;
            border-radius: 10px;
            padding: 10px;
            margin-top: 20px;
        }

        .text {
            padding-bottom: 10px;
            margin-right: 30px;
            text-align: center;
            width: fit-content;
        }

        .empty-box {
            width: 100px;
            height: 80px;
            border: 2px solid #898;
            border-radius: 10px;
            margin-top: -2px;
        }

        .print-button {
            display: block;
            margin-top: 20px;
            text-align: center;
        }

        @media print {
            body {
                margin: 0;
            }

            .print-button {
                display: none;
            }

            /* Menghilangkan waktu yang dicetak oleh browser */
            .date,
            .time {
                display: none;
            }

            /* Menghilangkan URL yang dicetak oleh browser */
            .url {
                display: none;
            }
        }
    </style>
</head>

<body>
    <button class="print-button" onclick="printPage()">Cetak Halaman</button>

    <h1 style="text-align: center;">{{ $data->title }}</h1>
    @foreach ($data->babs as $bab)
        <div style="display: flex; align-items: center;">
            <h2 style="margin-right: 5px;">BAB : {{ $bab->title }}</h2>
            <hr style="flex-grow: 1; height: 0; border-top: 1px solid black; margin: 0;">
        </div>
        @foreach ($bab->chapters as $chapter)
            <div style="display: flex; align-items: center;">
                <h3 style="margin-right: 5px;">{{ $chapter->translate }}</h3>
                <hr style="flex-grow: 1; height: 0; border-top: 1px solid black; margin: 0;">
            </div>
            <div style="direction: rtl;flex-wrap: wrap;display: flex; justify-content: flex-start;">
                @foreach ($chapter->words as $word)
                    <div class="text-container">
                        <div class="text">{{ $word->arab }}</div>
                        <div class="empty-box"></div>
                    </div>
                @endforeach
            </div>
            <p>{{ $chapter->description }}</p>
        @endforeach
    @endforeach
    <script>
        function printPage() {
            window.print();
        }

        document.addEventListener("DOMContentLoaded", function() {
            var textContainers = document.querySelectorAll('.text-container');
            textContainers.forEach(function(container) {
                var text = container.querySelector('.text');
                var emptyBox = container.querySelector('.empty-box');
                var textWidth = text.offsetWidth;
                emptyBox.style.width = (textWidth + 60) + 'px';
            });
        });
    </script>
</body>

</html>
