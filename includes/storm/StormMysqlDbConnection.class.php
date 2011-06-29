<?

class StormMysqlDbConnection extends StormDbConnection
{
    protected $handler = NULL;
    protected $cursor  = NULL;
    
    /**
        ����������� � ���� ������.
        ��� ��������� ����������� �� ������ ������.
        ���������� true, ���������� Exception �� �������.
    */
    function open()
    {
        // ���� ���������� � �����?
        if( $this->handler )
        {
            // �� ��������� � ��� � �������, ������ �� ������.
            if( @mysql_ping( $this->handler ) === true ) return true;

            // �� ���������, ���������� ������, ������ ����������
            $this->close();
            $this->open();
        }
        // ���������� ����, ����� ����������
        else
        {
            // �����������
            $handler = @mysql_connect(
                $this->host . ( $this->port ? ":{$this->port}" : '' ),
                $this->user,
                $this->password,
                true    // ������ ��������� ����� ����������
            );

            if( ! $handler )
            {
                throw new StormException( mysql_error() );
            }

            // ��������� ������� ���� ������, �������� ���������
            if( @mysql_select_db( $this->name, $handler ) !== true )
            {
                $error = mysql_error();
                mysql_close( $handler );
                throw new StormException( $error );
            }

            // ��������� ��������� ����������
            if( $this->charset )
            {
                @mysql_query( "SET NAMES '{$this->charset}'", $handler );
                if( @mysql_error() )
                {
                    $error = mysql_error();
                    mysql_close( $handler );
                    throw new StormException( $error );
                }
            }

            // ��� ������ ������ - �������� ���� �� ����������
            $this->handler = $handler;
            $this->cursor = NULL;
        }
    }

    /**
        �������� ����������
    */
    function close()
    {
        if( $this->handler ) @mysql_close( $this->handler );
        
        $this->handler = NULL;
        $this->cursor = NULL;
        
        return true;
    }

    /**
        ������������� ������
    */
    function escape( $string )
    {
        $this->open();
        return mysql_real_escape_string( $string, $this->handler );
    }
    
    /**
        ������������� ����� ������� ��
    */
    function escapeName( $name )
    {
        if( mb_substr( $name, 0, 1, 'utf-8' ) == '`' && mb_substr( $name, mb_strlen( $name, 'utf-8' ) - 1, 1, 'utf-8' ) == '`' )
        {
            return $name;
        }

        return "`{$name}`";
    }
    
    /**
        ��������� �������� ����������������� ����, ������������� �����
    */
    function getLastInsertId( StormDbCursor $cursor = NULL, $table='', $pkname='' )
    {
        return mysql_insert_id( $this->handler );
    }
    
    /**
        ��������� handler� ����������
    */
    function getHandler()
    {
        $this->open();
        return $this->handler;
    }
    
    /**
        ��������� ������� ����������
    */
    function getCursor()
    {
        $this->open();

        if( ! $this->cursor ) $this->cursor = new StormMysqlDbCursor( $this );
        
        return $this->cursor;
    }    
}

?>
