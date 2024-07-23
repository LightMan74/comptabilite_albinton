var tfConfig = {
    base_path: 'CSS_JS/tablefilter/',

    auto_filter: { delay: 10000 },

    extensions: [
        {
            name: 'colsVisibility',
            at_start: filteratstart,
            text: 'Columns: ',
            // enable_tick_all: true,
            toolbar_position: 'left'
        },
        //     {
        //     name: 'filtersVisibility',
        //     visible_at_start: true
        // },
        // {
        //    name: 'sort'
        //     },
    ],

    state: {// Enable state persistence
        types: ['cookie'], // Possible values: 'local_storage' 'hash' or 'cookie
        filters: true,// Persist filters values, enabled by default        
        columns_visibility: true,// Persist columns visibility
        filters_visibility: false,// Persist filters row visibility
        page_number: true,
        page_length: true,
        sort: true
    },

    ignore_diacritics: true,
    sticky_headers: true,					// Sticky headers with overrides  
    alternate_rows: true,					// Enable alternating rows  
    mark_active_columns: {					// Mark active columns  
        highlight_column: false,				// .. but only the column header  
    },

    paging: false,
    // paging: {
    //     results_per_page: ['Nbres/Pages: ', [100, 10, 25, 50, 100, 250, 500, 50000]]
    // },

    rows_counter: {
        text: 'Items: '
    }, // Enable rows counter UI component
    btn_reset: true,
    btn_reset: {
        text: 'RESET FILTRE'
    },
    // loader: true,// Loading indicator, { html : '...' }
    status_bar: true,

    grid_layout: false,						// Grit layout (but no simple copy&paste to Excel anymore)  
    filters_row_index: 0,  					// Row index to show the filter bar (default 0).

    locale: 'fr',

    thousands_separator: '',					// Define thousands separator ', ' or '.', defaults to ', '  
    decimal_separator: '.',					// Define decimal separator ',' or '.', defaults to '.'  
    empty_operator: '[vide]',
    nonempty_operator: '[nonvide]',

    toolbar: true,	// Enable toolbar component
    // toolbar: { target_id: 'externalToolbar' },

    // grid_layout: {
    //     width: '100%'
    // },


    highlight_keywords: true,

    load_filters_on_demand: false, //true

    no_results_message: true,
    watermark: ['id', 'timestamp', 'DATE_FACTURE', 'IDMOIS', 'DEBIT', 'CREDIT', 'TYPE', 'TTC', 'CLIENTS_FOURNISEUR', 'REMARQUES_DIVERSES', 'DATE_PAYEMENT', 'MONTANT', 'OPTIONS'],
    //['id', 'timestamp', 'N_FACTURE', 'DATE_FACTURE', 'IDMOIS', 'DEBIT', 'CREDIT', 'TYPE', 'TVA', 'HT', 'TVA', 'TTC', 'T_HT', 'T_TVA', 'T_TTC', 'CLIENTS_FOURNISEUR', 'REMARQUES_DIVERSES', 'DATE_PAYEMENT', 'CB', 'VIR', 'ESP', 'CHQ', 'BANQUE', 'N_CHEQUE', 'TITULAIRE_CHEQUE', 'TOTAL_PAYMENT', 'RBS', 'COMPTE_DEBIT', 'COMPTE_CREDIT', 'OPTIONS', 'ISEEROR', 'UPLOAD_COMPTA', 'COPIE'],
    col_types: [
        'number',
        { type: 'date', locale: 'fr', format: ['{yyyy}-{MM}-{dd} {hh}:{mm}:{ss}'] },
        { type: 'date', locale: 'fr', format: ['{dd}/{MM}/{yyyy}'] },
        { type: 'date', locale: 'fr', format: ['{yyyy}-{MM}'] },
        'string',
        'string',
        'string',
        'number',
        'string',
        'string',
        { type: 'date', locale: 'fr', format: ['{dd}/{MM}/{yyyy}'] },
        'number',
        'string',
        'string',
    ],

    // col_0: 'none',
    // col_1: 'none',
    col_6: 'select',
    // col_8: 'select',
    // col_27: 'select',
    // col_28: 'select',
    // col_29: 'none',
    // col_30: 'select',
    // col_31: 'select',
    col_12: 'none',

};

var tf = new TableFilter('searchtable', tfConfig);
// Subscribe to events
// Format cell at initialization
tf.emitter.on(['initialized'], parseRows);
// Format cell upon filtering
tf.emitter.on(['cell-processed'], formatCell);

// tf.emitter.on(['column-calc'], calcAll);
tf.init();



// Process all rows on start-up
function parseRows(tf) {
    //Condition for TOTAL_PAYEMENT
    var rowsIdx = tf.getValidRows();
    rowsIdx.forEach(function (idx) {
        var row = tf.dom().rows[idx];

        var cell = row.cells[25];
        var cellref = row.cells[14];
        var cellref1 = row.cells[18];
        var cellref2 = row.cells[19];
        var cellref3 = row.cells[20];
        var cellref4 = row.cells[21];
        var cellref5 = row.cells[30];
        formatCell(tf, cell, 'TOTAL_PAYEMENT', cellref, cellref1, cellref2, cellref3, cellref4, cellref5);

        var cell = row.cells[17];
        formatCell(tf, cell, 'DATE_PAYEMENT');

        var cell = row.cells[27];
        formatCell(tf, cell, 'COMPTE');

        var cell = row.cells[15];
        formatCell(tf, cell, 'CLIENT');

        var cell = row.cells[21];
        var cellref = row.cells[22];
        var cellref1 = row.cells[23];
        var cellref2 = row.cells[24];
        formatCell(tf, cell, 'CHQ', cellref, cellref1, cellref2);

        var cell = row.cells[31];
        var cellref = row.cells[2];
        formatCell(tf, cell, 'UPLOAD', cellref);
    });
}

// Format passed cell with custom logic
function formatCell(tf, cell, forcondition, cellref = 0, cellref1 = 0, cellref2 = 0, cellref3 = 0, cellref4 = 0, cellref5 = 0) {

    var cellData = '';
    var cellrefData = '';

    switch (forcondition) {
        case 'TOTAL_PAYEMENT':
            cellData = parseFloat(cell.innerHTML);
            cellrefData = parseFloat(cellref.innerHTML);
            iserror = parseFloat(cellref5.innerHTML);
            if (isNaN(cellData)) {
                return;
            }
            if (cellData > cellrefData && (iserror != '1')) {
                cell.style.backgroundColor = '#69ad02';
            } else if (((cellData < cellrefData) && (iserror == '0')) || (iserror == '2')) {
                cell.style.backgroundColor = '#fcb605';
                cellref.style.backgroundColor = '#fcb605';
                cellref1.style.backgroundColor = '#fcb605';
                cellref2.style.backgroundColor = '#fcb605';
                cellref3.style.backgroundColor = '#fcb605';
                cellref4.style.backgroundColor = '#fcb605';
            }
            break;
        case 'DATE_PAYEMENT':
            cellData = cell.innerHTML;
            if (((cellData === '') && (iserror == '0')) || (iserror == '2')) {
                cell.style.backgroundColor = '#fcb605';
            }
            break;
        case 'COMPTE':
            cellData = cell.innerHTML;
            if ((cellData === '') && (iserror == '0')) {
                cell.style.backgroundColor = '#fcb605';
            }
            break;
        case 'CLIENT':
            cellData = cell.innerHTML;
            if (((cellData === '') && (iserror == '0')) || (iserror == '2')) {
                cell.style.backgroundColor = '#fcb605';
            }
            break;
        case 'CHQ':
            cellData = parseFloat(cell.innerHTML);
            cellData0 = cellref.innerHTML;
            cellData1 = cellref1.innerHTML;
            cellData2 = cellref2.innerHTML;
            if (cellData != '0.00') {
                if ((cellData0 === '' || cellData1 === '' || cellData2 === '') && (iserror == '0')) {
                    cell.style.backgroundColor = '#fcb605';
                    cellref.style.backgroundColor = '#fcb605';
                    cellref1.style.backgroundColor = '#fcb605';
                    cellref2.style.backgroundColor = '#fcb605';
                }
            }
            break;
        case 'UPLOAD':
            // console.debug(cell.innerHTML)
            // console.debug(parseFloat(cell.innerHTML))
            cellData = parseFloat(cell.innerHTML);
            if (cellData == '1') {
                cell.style.backgroundColor = '#69ad02';
                cellref.style.backgroundColor = '#69ad02';
            }
            break;

    }



}