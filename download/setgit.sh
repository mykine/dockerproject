#!/bin/bash
echo "下载git"
wget -P ~/download https://github.com/mykine/dockerproject/raw/master/download/git-2.9.5.tar.gz
cd ~/download
echo "解压"
tar -zxvf git-2.9.5.tar.gz

yum -y install perl-ExtUtils-CBuilder perl-ExtUtils-MakeMaker

echo "安装开始"
cd git-2.9.5 
mkdir /usr/local/git295
echo "执行configure..."
./configure --prefix=/usr/local/git295
echo "make..."
make
echo "make install ..."
make install
ln -s /usr/local/git295/bin/git /usr/local/bin/git
echo "安装结束，目录 /usr/local/git295 "
git --version

