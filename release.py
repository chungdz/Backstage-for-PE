# FTP操作
import paramiko
import os

def sftp_upload(host,port,username,password,local,remote):
    sf = paramiko.Transport((host,port))
    sf.connect(username = username,password = password)
    sftp = paramiko.SFTPClient.from_transport(sf)
    try:
        if os.path.isdir(local):#判断本地参数是目录还是文件
            for root, dirs, files in os.walk(local, topdown=False):
                for name in files:
                    src = os.path.join(root, name)
                    repath = root.split(local)[1]
                    dst = os.path.join(remote, repath, name).replace('\\','/')
                    sftp.put(src ,dst)
                    print(src+' to '+dst)
        else:
            sftp.put(local,remote)#上传文件
    except Exception as e:
        print(f)
        print('upload exception:', e)
    sf.close()

def sftp_download(host,port,username,password,local,remote):
    sf = paramiko.Transport((host,port))
    sf.connect(username = username,password = password)
    sftp = paramiko.SFTPClient.from_transport(sf)
    try:
        if os.path.isdir(local):#判断本地参数是目录还是文件
            for f in sftp.listdir(remote):#遍历远程目录
                 sftp.get(os.path.join(remote+f),os.path.join(local+f))#下载目录中文件
        else:
            sftp.get(remote,local)#下载文件
    except Exception as e:
        print('download exception:', e)
    sf.close()


def main():
    host = '47.94.19.15'
    port = 22
    username = 'root'
    password = 'Hyf970527'
    local = 'F:\\Web\\Backstage-for-PE\\build\\'
    remote = '/var/www/html/ch/'
    sftp_upload(host, port, username, password, local, remote)

if __name__ == '__main__':
    main()