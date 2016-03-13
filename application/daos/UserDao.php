<?php
class UserDao extends CxDao{

    public function query(){
        $ms = CxMssql::getInstance();
        $result = $ms->in('@id','str',2)
            ->output('@ret_status')
            ->output('@ret_desc')
            ->spName('dbo.sp_test_return_out')
            ->calls();
        e($result);

        $result = $ms->output('@a','int')
            ->output('@b','int')
            ->in('@c','str','test')
            ->spName('dbo.pro_test_int')
            ->call();
        ed($result);
    }

    public function ms(){
        $msdb=mssql_connect("172.16.66.133:1433","sa","123456");
        if (!$msdb) {
            echo "connect sqlserver error";
            exit;
        }
        mssql_select_db("cxmvc",$msdb);
        $result = mssql_query("SELECT top 5 * FROM users", $msdb);
        while($row = mssql_fetch_array($result)) {
            e($row);
        }
        mssql_free_result($result);
    }

    public function mssql(){
        $ms = CxMssql::getInstance();
        $result = $ms->setOutput('@a','int')
            ->setOutput('@b','int')
            ->setParam('@c','str','test')
            ->setProName('dbo.pro_test_int')
            ->calls();
        ed($result);

        $conn=mssql_connect("172.16.66.133","sa","123456");
        mssql_select_db("cxmvc");
        $stmt=mssql_init("dbo.pro_test_int",$conn) or die("initialize stored procedure failure");

        $data = array();
        $a = 0;
        $b = 0;
        $out = array();
        $c = "abandonship";
        //SQLTEXT, SQLVARCHAR, SQLCHAR, SQLINT1, SQLINT2, SQLINT4, SQLBIT, SQLFLT4, SQLFLT8, SQLFLTN
        mssql_bind($stmt,"@c",$c,SQLVARCHAR);
        mssql_bind($stmt,"@b",$out['@b'],SQLINT4,true);//用于返回在存储过程中定义的输出参数
        mssql_bind($stmt,"@a",$out['@a'],SQLINT4,true); //用于直接返回return -103此类的值。

        $result = mssql_execute($stmt,false); //返回结果集
        do{
            $rest = array();
            while($records = mssql_fetch_assoc($result)){
                $rest[] = $records;
            }
            $data['table'][] = $rest;
        }
        while(mssql_next_result($result));
        $data['out'] = $out;
        e($data);
    }

    public function data(){
        try {
            $hostname = "172.16.66.133";
            $port = 1433;
            $dbname = "cxmvc";
            $username = "sa";
            $pw = "123456";
            $dbh = new PDO ("dblib:host=$hostname:$port;dbname=$dbname",$username,$pw);

            $procName = "pro_test_int";

            $a = 0;
            $b = "";
            $c = "abandonship";


            $query = $dbh->query("EXEC $procName",PDO::FETCH_ASSOC);
            ed($query);
            do {
                $results = $query->fetchAll();
                e($results);
            }
            while ($query->nextRowset());
            $query->closeCursor();
            die();
            ed($results);
            //"EXEC $procName ?, ?, ?"
            $stmt = $dbh->prepare("SELECT top 5 * FROM users");

            //PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE
            $stmt->bindParam(1, $a, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4);
            $stmt->bindParam(2, $b, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4);
            $stmt->bindParam(3, $c, PDO::PARAM_STR);

            $stmt->execute();
            do{
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                e($a);
                e($b);
                e($result);
            }
            while($stmt->nextRowset());
            ed('while');

            //$stmt = $dbh->prepare("SELECT top 5 * FROM users");
            //$stmt->execute();
            //while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //    e($row);
            //}
            //unset($dbh); unset($stmt);
        }
        catch (PDOException $e) {
            unset($dbh); unset($stmt);
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            exit;
        }
    }

    public function data2(){
        try {
            $hostname = "172.16.66.133";
            $port = 1433;
            $dbname = "cxmvc";
            $username = "sa";
            $pw = "123456";
            $dbh = new PDO ("dblib:host=$hostname:$port;dbname=$dbname",$username,$pw);

            $procName = "pro_test";
            $stmt = $dbh->prepare("EXEC $procName ?, ?, ?");
            $nReturnValue = 0;
            $strReturnValue = "";
            $strSearchValue = "abandonship";
            //PDO::SQLSRV_PARAM_OUT_DEFAULT_SIZE
            $stmt->bindParam(1, $nReturnValue, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 4);
            $stmt->bindParam(2, $strReturnValue, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 10);
            $stmt->bindParam(3, $strSearchValue , PDO::PARAM_STR);

            $stmt->execute();
            do{
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                //e($nReturnValue);
                //e($strReturnValue);
                e($result);
            }while($stmt->nextRowset());
            ed('while');
            //获取第一个结果集.
            $rowset_1 = $stmt->fetch(PDO::FETCH_ASSOC);
            e($rowset_1);
            echo '<br><br>';

            //获取第二个结果集.
            $stmt->nextRowset();
            $rowset_2 = $stmt->fetch(PDO::FETCH_ASSOC);
            e($rowset_2);
            echo '<br><br>';
            $stmt->nextRowset();
            ed('end2');
            // 获取两个输出类型的参数
            echo $nReturnValue.'<br><br>';
            echo $strReturnValue;

            //$stmt = $dbh->prepare("SELECT top 5 * FROM users");
            //$stmt->execute();
            //while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            //    e($row);
            //}
            //unset($dbh); unset($stmt);
        }
        catch (PDOException $e) {
            unset($dbh); unset($stmt);
            echo "Failed to get DB handle: " . $e->getMessage() . "\n";
            exit;
        }
    }
	
}