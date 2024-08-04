<?php
// use Fpdf\Fpdf;
// // use setasign\Fpdi\Fpdi;
// require "vendor/autoload.php";


// require_once('vendor/fpdf/fpdf.php');

class PDF_MySQL_Table extends FPDF
{
protected $ProcessingTable=false;
protected $aCols=array();
protected $TableX;
protected $HeaderColor;
protected $RowColors;
protected $ColorIndex;

function Header()
{
    // Print the table header if necessary
    if($this->ProcessingTable)
        $this->TableHeader();
}

function TableHeader()
{
    $this->SetFont('Arial','B',12);
    $this->SetX($this->TableX);
    $fill=!empty($this->HeaderColor);
    if($fill)
        $this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
    foreach($this->aCols as $col)
        $this->Cell($col['w'],6,$col['c'],1,0,'C',$fill);
    $this->Ln();
}

function Row($data)
{
    $this->SetX($this->TableX);
    $ci=$this->ColorIndex;
    $fill=!empty($this->RowColors[$ci]);
    // if($fill){
    //     $this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
    // }
    // if($data['comptenull']==1){
    //     $this->SetFillColor(255,165,0);
    // }  
    foreach($this->aCols as $col){
        if (!array_key_exists("MorA", $data)){
            if($data['ISERROR']==2||($data['comptenull']==1 && $col['f'] == 'COMPTE')||($data['compte_enull']==1 && $col['f'] == 'COMPTE_E')||($data['idmoiserror']==1 && $col['f'] == 'IDMOIS')){
                $this->SetFillColor(255,50,50);
            }elseif($data['totalpayementup']==1 && ($col['f'] == 'CLIENTS_FOURNISEUR'||$col['f'] == 'T_TTC')||($data['totalpayementup']==1 && ($col['f'] == 'DATE_PAYEMENT'||$col['f'] == 'REMARQUE_DIVERSE'||$col['f'] == 'CB'||$col['f'] == 'VIR'||$col['f'] == 'ESP'||$col['f'] == 'CHQ'||$col['f'] == 'BANQUE'||$col['f'] == 'N_CHEQUE'||$col['f'] == 'TITULAIRE_CHEQUE'||$col['f'] == 'TOTAL_PAYEMENT'))){
                $this->SetFillColor(50,205,50);       
            }elseif($data['totalpayementdown']==1 && ($col['f'] == 'CLIENTS_FOURNISEUR'||$col['f'] == 'T_TTC')||($data['totalpayementdown']==1 && ($col['f'] == 'DATE_PAYEMENT'||$col['f'] == 'REMARQUE_DIVERSE'||$col['f'] == 'CB'||$col['f'] == 'VIR'||$col['f'] == 'ESP'||$col['f'] == 'CHQ'||$col['f'] == 'BANQUE'||$col['f'] == 'N_CHEQUE'||$col['f'] == 'TITULAIRE_CHEQUE'||$col['f'] == 'TOTAL_PAYEMENT'))){
                $this->SetFillColor(255,165,0);                
            }elseif($data['UPLOAD_COMPTA']==1 && $col['f'] == 'N_FACTURE'){
                $this->SetFillColor(50,205,50);        
            }else{
                $this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
            }        
        }else{
            $this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
        }
        
        if (array_key_exists("MorA", $data)&&$col['f'] != 'MorA'){
        $this->Cell($col['w'],5,number_format($data[$col['f']], 2, ',', ' '),1,0,$col['a'],$fill);
        }else{
        $this->Cell($col['w'],5,$data[$col['f']],1,0,$col['a'],$fill);
        }
}
    $this->Ln();
    $this->ColorIndex=1-$ci;
}

function CalcWidths($width, $align)
{
    // Compute the widths of the columns
    $TableWidth=0;
    foreach($this->aCols as $i=>$col)
    {
        $w=$col['w'];
        if($w==-1)
            $w=$width/count($this->aCols);
        elseif(substr($w,-1)=='%')
            $w=$w/100*$width;
        $this->aCols[$i]['w']=$w;
        $TableWidth+=$w;
    }
    // Compute the abscissa of the table
    if($align=='C')
        $this->TableX=max(($this->w-$TableWidth)/2,0);
    elseif($align=='R')
        $this->TableX=max($this->w-$this->rMargin-$TableWidth,0);
    else
        $this->TableX=$this->lMargin;
}

function AddCol($field=-1, $width=-1, $caption='', $align='L')
{
    // Add a column to the table
    if($field==-1)
        $field=count($this->aCols);
    $this->aCols[]=array('f'=>$field,'c'=>$caption,'w'=>$width,'a'=>$align);
}

function Table($link, $query, $prop=array())
{
    // Execute query
    $res=mysqli_query($link,$query) or die('Error: '.mysqli_error($link)."<br>Query: $query");
    // Add all columns if none was specified
    if(count($this->aCols)==0)
    {
        $nb=mysqli_num_fields($res);
        for($i=0;$i<$nb;$i++)
            $this->AddCol();
    }
    // Retrieve column names when not specified
    foreach($this->aCols as $i=>$col)
    {
        if($col['c']=='')
        {
            if(is_string($col['f']))
                $this->aCols[$i]['c']=ucfirst($col['f']);
            else
                $this->aCols[$i]['c']=ucfirst(mysqli_fetch_field_direct($res,$col['f'])->name);
        }
    }
    // Handle properties
    if(!isset($prop['width']))
        $prop['width']=0;
    if($prop['width']==0)
        $prop['width']=$this->w-$this->lMargin-$this->rMargin;
    if(!isset($prop['align']))
        $prop['align']='C';
    if(!isset($prop['padding']))
        $prop['padding']=$this->cMargin;
    $cMargin=$this->cMargin;
    $this->cMargin=$prop['padding'];
    if(!isset($prop['HeaderColor']))
        $prop['HeaderColor']=array();
    $this->HeaderColor=$prop['HeaderColor'];
    if(!isset($prop['color1']))
        $prop['color1']=array();
    if(!isset($prop['color2']))
        $prop['color2']=array();
    $this->RowColors=array($prop['color1'],$prop['color2']);
    // Compute column widths
    $this->CalcWidths($prop['width'],$prop['align']);
    // Print header
    $this->TableHeader();
    // Print rows
    $this->SetFont('Arial','',11);
    $this->ColorIndex=0;
    $this->ProcessingTable=true;
    while($row=mysqli_fetch_array($res))
        $this->Row($row);
    $this->ProcessingTable=false;
    $this->cMargin=$cMargin;
    $this->aCols=array();
}
}
?>