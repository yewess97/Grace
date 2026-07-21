@extends(key(viewLayoutTitle(USER_MODEL)), current(viewLayoutTitle(USER_MODEL)))

@section('user-css-links')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.9.0/github-markdown.min.css">
@endsection

@section('user-css')
    <style nonce="{{$nonce}}">
        body {
            color: var(--black-color);
        }

        .markdown-body ul {
            list-style: disc;
        }

        .markdown-body ol {
            list-style: decimal;
        }

        .markdown-body :is(ul, ol) {
            line-height: inherit;
        }

        .markdown-body {
            padding: var(--thirty-pixels);
            color: inherit;
        }

        .markdown-body table {
            color: var(--white-color);
        }

        .mermaid {
            display: flex;
            justify-content: center;
            margin-block: var(--twenty-pixels);
            padding-block: var(--forty-five-pixels);
            background-color: #0d1117;
        }

        .mermaid p {
            margin-bottom: 0;
            color: var(--white-color);
            line-height: inherit;
        }

        .mermaid .flowchart .marker {
            fill: var(--light-gray-color);
        }

        .mermaid .erDiagram {
            padding-inline: var(--thirty-pixels);
        }

        .mermaid :is(.erDiagram .marker, .edgePaths :where(.flowchart-link, .relationshipLine)) {
            fill: none;
        }

        .mermaid :is(.erDiagram .marker, .edgePaths :where(.flowchart-link, .relationshipLine), .nodes .node rect) {
            stroke: var(--light-gray-color);
            stroke-width: 1px;
        }

        .mermaid .edgePaths .edge-pattern-solid {
            stroke-dasharray: 0;
        }

        .mermaid .edgeLabels .edgeLabel .label div {
            background-color: hsl(0, 0%, 34.4117647059%);
            text-align: center;
        }

        .mermaid .nodes .node :is(rect, polygon) {
            fill: #1f2020;
        }
    </style>
@endsection

@section('content')

    {{-- Documentation Main --}}
    <main role="main" class="documentation-main">
        <div class="container">
            <article class="markdown-body mx-auto bg-transparent">
                {!! $html_content !!}
            </article>
        </div>
    </main>

@endsection

@section('user-js-links')
    <script type="application/javascript" src="https://cdn.jsdelivr.net/npm/mermaid@11.16.0/dist/mermaid.min.js"></script>
@endsection

@section('user-js')
    <script nonce="{{$nonce}}" type="application/javascript">
        $(document).on("DOMContentLoaded", () => {
            mermaid.initialize({
                startOnLoad: true,
                theme: 'dark',
            });

            $('pre code.language-mermaid').each(function () {
                const pre_element = $(this).parent();
                const mermaid_div = $('<div class="mermaid">').text($(this).text());

                pre_element.replaceWith(mermaid_div);
            });
        });
    </script>
@endsection
