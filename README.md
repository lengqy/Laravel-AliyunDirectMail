# Aliyun Direct Mail For Laravel 5

使用阿里云的邮件推送（[DirectMail](https://www.aliyun.com/product/directmail)）发送邮件，

仅支持单一发信接口（[SingleSendMail](https://help.aliyun.com/document_detail/29444.html?spm=5176.doc29435.2.3.pHDCrc)）。

## 安装

> 在Laravel 5.4或更低版本，请使用

```bash
composer require cherrytools/directmail:~1.0
```

> 在Laravel 5.5或更高版本，请使用

```bash
composer require cherrytools/directmail:~2.0
```


## 配置

在 `.env` 中配置您的密钥， 并修改邮件驱动为 `directmail`
```bash
MAIL_DRIVER=directmail
DIRECT_MAIL_KEY=            # AccessKeyId
DIRECT_MAIL_SECRET=         # AccessKeySecret
```

您也可以进行一些额外的配置，具体请参考阿里云邮件推送[官方文档](https://help.aliyun.com/document_detail/29444.html?spm=5176.doc29435.2.3.pHDCrc)
```bash
DIRECT_MAIL_REPLAY_TO=      # ReplyToAddress
DIRECT_MAIL_ADDRESS_TYPE=   # AddressType
DIRECT_MAIL_REGION=         # RegionId
DIRECT_MAIL_CLICK_TRACE=    # ClickTrace
```
在Laravel 5.5或更高版本中，该软件包将被自动发现，您可以跳过以下步骤。

如果使用Laravel 5.4或更低版本，则需要将ServiceProvider添加到 `config/app.php` 中的 `providers` 数组中
```bash
Cherry\DirectMail\DirectMailTransportProvider::class,
```

## 使用

详细用法请参考 laravel 文档： 

> <http://d.laravel-china.org/docs/5.5/mail>

#### 演示
```bash
Mail::raw('Hello World!', function ($message) {
    $message->from('email', 'alias');
    // 单个收件人
    $message->to('email', 'alias');
    // 多个收件人
    $message->to(['email'=>'alias', 'email'=>'alias']);
    $message->subject('Hello World');
});
```

## 贡献

- <https://github.com/wangyan/directmail>
