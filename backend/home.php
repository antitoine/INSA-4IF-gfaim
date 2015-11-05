<!DOCTYPE html>
<html>
    <head>
        <title>API GFaim</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
        <style type="text/css">
            .module .well {
                min-height: 200px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="main container">
            <h1>API GFaim</h1>
            <div class="gfaim row">
                <div class="well">
                    <h2>Search recipes from ingredients</h2>
                    <form action="/search">
                        <div class="form-group">
                            <label for="queryGfaim">Ingredients</label>
                            <input id="queryGfaim" type="text" name="q"/>
                        </div>
                        <div class="form-group">
                            <label for="confidenceGfaim">Confidence</label>
                            <input id="confidenceGfaim" type="number" name="confidence" value="1"/>
                        </div>
                        <div class="form-group">
                            <label for="similarityGfaim">Confidence</label>
                            <input id="similarityGfaim" type="number" name="similarity" value="1.5"/>
                        </div>
                        <button class="btn btn-default" type="submit">Submit</button>
                    </form>
                </div>
            </div>
            <h2>Individual tests</h2>
            <div class="modules row">
                <div class="module module-1 col-sm-6">
                    <div class="well">
                        <h3>Module 1 : Search links with the Google API</h3>
                        <form action="/search/test">
                            <div class="form-group">
                                <label for="queryModule1">Query to search on google</label>
                                <input id="queryModule1" type="text" name="q"/>
                            </div>
                            <button class="btn btn-default" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="module module-2 col-sm-6">
                    <div class="well">
                        <h3>Module 2 : Extract text from URL</h3>
                        <form action="/extract/test">
                            <div class="form-group">
                                <label for="urlModule2">URL of the page</label>
                                <input id="urlModule2" type="text" name="url"/>
                            </div>
                            <button class="btn btn-default" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="module module-3 col-sm-6">
                    <div class="well">
                        <h3>Module 3 : Annotate relevent word from a text</h3>
                        <form action="/annotate/test">
                            <div class="form-group">
                                <label for="annotateModule3">Text to annotate</label>
                                <textarea id="annotateModule3" type="textarea" name="text"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="confidenceModule3">Confidence</label>
                                <input id="confidenceModule3" type="number" name="confidence" value="1"/>
                            </div>
                            <button class="btn btn-default" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="module module-4 col-sm-6">
                    <div class="well">
                        <h3>Module 4 : Extend data with SPARQL</h3>
                        <form action="/enhance/test">
                            <button class="btn btn-default" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="module module-5 col-sm-6">
                    <div class="well">
                        <h3>Module 5 : Get graph of similarity</h3>
                        <form action="/similarity/test">
                            <button class="btn btn-default" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <h2>Combinate tests</h2>
            <div class="modules row">
                <div class="module module-1-2 col-sm-12">
                    <div class="well">
                        <h3>Module 1 et 2 : Extract text from all URLs of a query</h3>
                        <form action="/search/extract/test">
                            <div class="form-group">
                                <label for="queryModule1And2">Query to search</label>
                                <input id="queryModule1And2" type="text" name="q"/>
                            </div>
                            <button class="btn btn-default" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
                <div class="module module-1-2-3 col-sm-12">
                    <div class="well">
                        <h3>Module 1, 2, 3 : Get relevent words of extracted URL pages found by the query</h3>
                        <form action="/search/extract/annotate/test">
                            <div class="form-group">
                                <label for="queryModule1And2And3">Query to search</label>
                                <input id="queryModule1And2And3" type="text" name="q"/>
                            </div>
                            <div class="form-group">
                                <label for="confidenceModule1And2And3">Confidence</label>
                                <input id="confidenceModule1And2And3" type="number" name="confidence" value="1"/>
                            </div>
                            <button class="btn btn-default" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>